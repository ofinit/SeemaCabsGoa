# Deploying Seema Cabs Goa on Coolify

One Docker image serves everything on a single domain:

| URL | Served by |
|---|---|
| `/`, `/airport-taxi-goa.html`, `/contact.html`, … | Static marketing site (repo root) |
| `/app/...` | Customer PWA (Laravel): `https://www.seemacabsgoa.com/app` |
| `/login`, `/dashboard`, … | Admin panel (Laravel, 2FA protected) |
| `/api/...` | REST API for the mobile apps (Laravel) |
| `/storage/...` | Uploaded images (persistent volume) |

The image is built from [`taxigo/Dockerfile`](Dockerfile) on top of
`serversideup/php:8.2-fpm-nginx` (nginx + PHP-FPM, listens on port **8080**).
The Docker build context is the **repository root**, because the marketing pages
live there and get copied into Laravel's `public/` directory during the build.

---

## 1. Before you start

- **Database.** The app uses its own MySQL database running on the Coolify
  server (section 2). The data is moved over from the old CloudJiffy database
  with a dump and import; see section 8 for the final cutover.
- **Frontend assets.** `taxigo/public/build` is committed and used as-is, so the
  server doesn't need Node. After changing anything in `resources/`, run
  `npm run build` inside `taxigo/` and commit the updated `public/build`.
- **Mobile apps.** Check which API base URL the Android and iOS apps use. If it
  is `https://seemacabsgoa.com/taxigo/api/...`, the apps must be updated to
  `https://www.seemacabsgoa.com/api/...` before DNS points at Coolify. Until then,
  keep the old server answering the old URL.
- **Collect from the current server:**
  - the current `APP_KEY`. Reuse it: a new key logs everyone out and breaks any
    encrypted values.
  - `storage/app/firebase/firebase_credentials.json`
  - the uploaded images (`storage/app/public/`, ~150 MB)
  - a database export (`.sql`), made with phpMyAdmin or `mysqldump`

## 2. Create the MySQL database in Coolify

1. In the **same project and environment** as the app: **+ New → Database →
   MySQL**, version **8.4**. This matches the old server (8.4.5).
2. Set **Database** to `taxigo` and choose a username. Let Coolify generate
   the passwords, and keep them only in Coolify.
3. Leave **Make it publicly available** **off**. The app reaches the database
   over Coolify's internal Docker network, so port 3306 never needs to be open
   to the internet.
4. Under **Backups**, enable scheduled backups (e.g. daily) to S3-compatible
   storage, so the database is never backed up only on the same disk.
5. **Start** it, and note its **internal hostname** (the container name /
   UUID shown under *Internal URL*). This goes in `DB_HOST`.

### Import the data

The dump is a plain phpMyAdmin/mysqldump file. It has no views, triggers,
procedures or `DEFINER` clauses, uses `utf8mb4_unicode_ci`, and has no
`CREATE DATABASE` line, so it imports into the `taxigo` database you
created. Copy it to the server over SSH (it contains customer data), then run:

```bash
# On the Coolify server. <db-container> is the database's container name
# (docker ps | grep mysql); the root password is on its Coolify page.
docker exec -i <db-container> \
  mysql -uroot -p'<root-password>' --default-character-set=utf8mb4 taxigo < taxigo.sql

# Sanity check: should list 43 tables
docker exec -i <db-container> mysql -uroot -p'<root-password>' -e "SHOW TABLES" taxigo | wc -l

# Delete the dump from the server afterwards
rm taxigo.sql
```

## 3. Create the application in Coolify

1. **+ New → Application → Private Repository (with GitHub App)** and select
   `ofinit/SeemaCabsGoa`, branch `main`.
2. **Build Pack:** `Dockerfile`.
3. **Base Directory:** `/`
4. **Dockerfile Location:** `/taxigo/Dockerfile`
5. **Ports Exposes:** `8080`
6. **Domains:** `https://www.seemacabsgoa.com,https://seemacabsgoa.com`, and under
   **Advanced → Redirect Direction** choose **Redirect to www**. `www` is the
   canonical host: the sitemap and `robots.txt` use it, and Coolify issues the
   TLS certificates for both names.
7. **Health check** (optional): path `/healthcheck`, port `8080`.

## 4. Environment variables

Add these under **Environment Variables**. Never commit a `.env` file.

| Variable | Value |
|---|---|
| `APP_NAME` | `Seema Cabs Goa` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(the existing key from the current server)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://www.seemacabsgoa.com` |
| `LOG_LEVEL` | `error` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` | *(internal hostname of the Coolify MySQL, section 2)* / `3306` |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | `taxigo` / *(user and password from the Coolify MySQL page)* |
| `MAIL_MAILER` | `smtp` |
| `BROADCAST_DRIVER` | `log` |
| `SESSION_DRIVER` | `database` (sessions survive deploys; needs the migrations) |
| `SESSION_SECURE_COOKIE` | `true` |
| `CACHE_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `local` |

The Razorpay/Cashfree keys and the SMTP mail settings are **not** environment
variables. They live in the `environments` database table and are managed
from the admin panel, so they arrive with the database import.

Leave **Build Variable** unchecked for all of these. They are only needed at
runtime, and this keeps secrets out of the image layers.

## 5. Persistent storage

Under **Persistent Storage**:

| Type | Destination path in container | Purpose |
|---|---|---|
| Volume | `/var/www/html/storage/app/public` | Uploaded images: driver documents, cab photos, banners. **Required**; without it, uploads are lost on every deploy. |
| File | `/var/www/html/storage/app/firebase/firebase_credentials.json` | Paste the Firebase service-account JSON. Required for push notifications. |
| Volume | `/var/www/html/storage/app/ads` | Self-serve ads: advertisers' licence documents and original ad images. **Private** (never served publicly). **Required** once self-serve ads are on. |

### Copy the existing uploads into the volume

After the first deploy has created the volume, copy the old
`storage/app/public/` contents onto the Coolify server and run these commands there:

```bash
# Find the volume name (Coolify shows it on the Persistent Storage tab)
docker volume ls | grep storage

# Copy the files in and give them to www-data (uid 33 in this image)
sudo rsync -a ./old-uploads/ /var/lib/docker/volumes/<volume-name>/_data/
sudo chown -R 33:33 /var/lib/docker/volumes/<volume-name>/_data/
```

These files include Aadhaar cards and driving licences. Transfer them over
SSH/SFTP only, delete any temporary copies afterwards, and never commit them.

## 6. Deploy

Click **Deploy**. When the container starts it automatically runs
`storage:link` and caches the config, routes, views and events.

**Migrations do not run automatically**, because the database is the live
production database. When a release includes new migrations, back up the
database first, then run this in the Coolify **Terminal** for the app:

```bash
php artisan migrate --force
```

## 7. Verify

- [ ] `https://www.seemacabsgoa.com/` shows the marketing homepage, and `https://seemacabsgoa.com/` redirects to it
- [ ] `/airport-taxi-goa.html` and the other marketing pages load with styles and images
- [ ] `https://www.seemacabsgoa.com/app` loads the customer PWA; it can be installed to the home screen and OTP login works
- [ ] `/login` shows the admin login; 2FA works
- [ ] An existing uploaded image (e.g. a cab photo in the admin panel) displays
- [ ] A test booking and payment (Razorpay/Cashfree) go through
- [ ] A push notification arrives (Firebase credentials mounted)
- [ ] The mobile apps can reach `/api/...`
- [ ] Coolify **Logs** show no errors (Laravel logs go to stderr)

## 8. Go-live cutover (final data sync)

Everything above can be tested with an earlier dump while the old site keeps
running. Bookings made on the old site after that dump are **not** in the new
database, so on go-live day:

1. Pause the old site: put it in maintenance mode (`php artisan down`) or
   switch off booking, so no new data is written.
2. Export a **fresh** dump from the old CloudJiffy database.
3. Re-import it into the Coolify MySQL (section 2). Drop and recreate the
   `taxigo` database first so no test data is left over.
4. Re-sync the uploads into the volume (section 5). `rsync` only copies the
   new files.
5. Point DNS for `seemacabsgoa.com` and `www.seemacabsgoa.com` at the Coolify
   server, then run through the checklist in section 7.
6. Keep the old server and database untouched, but paused, for a few days as a
   fallback, then decommission them.

## 9. Updating

Push to `main`, then click **Redeploy**, or enable Coolify's auto-deploy webhook.
The uploads volume, sessions volume, Firebase file and the MySQL database are
kept between deploys.

## 10. Scheduled tasks

The app's scheduler drafts last month's OfinIT platform-fee and ad-platform
invoices on the 1st at 06:00 IST (an admin reviews and issues them), and runs
`ads:maintain` every hour (expires ended ads, sends renewal reminders, confirms
ad payments the browser didn't, pauses ads with expired licences or broken
links) and `ads:weekly-reports` on Mondays at 10:00 IST (needs working SMTP
settings). In Coolify go to the app's
**Scheduled Tasks → + Add**:

| Name | Command | Frequency |
|---|---|---|
| Laravel scheduler | `php artisan schedule:run` | `* * * * *` |

Without it, use **Admin → Invoices → Prepare draft** each month instead.

## 11. Pricing, GST & invoicing (first-time setup)

Release `pricing v2` adds database tables and columns. **Run the migrations
right after deploying it**, after taking a database backup (section 6):

```bash
php artisan migrate --force
```

They move the old 20% "GST" setting into an internal **fare markup**, so
customer prices don't change. GST itself starts **switched off**. Then, in the
admin panel (super-admin login):

1. **Settings → Business Profiles:** enter the legal name, GSTIN, PAN, address
   and invoice prefix for **Seema Holidays** and **OfinIT Solutions Pvt. Ltd.**
   Documents are saved as numberless drafts until a profile is complete.
2. **Settings → Pricing:** fare markup (rides / packages), OfinIT platform fee
   %, and the online advance %. Every change is logged.
3. **Settings → GST** (after CA sign-off): SAC codes, rates, and the date-time
   from which bookings are charged GST, then switch it on. Bookings made
   before that moment are never recalculated. To keep customer totals the same
   when GST starts, set the markup to 14.29% (for 20% markup + 5% GST).
4. **Invoices:** customer receipt vouchers, tax invoices, refund vouchers and
   credit notes are created automatically. Prepare, review and issue the
   monthly OfinIT invoice here, and download GSTR-1 CSVs per business.

## Security notes

- There are no web URLs that run Artisan commands; the old public `/clear`,
  `/migrate` and `/in-city-rides` routes were removed. Use the Coolify
  **Terminal** instead, e.g. `php artisan optimize:clear`.
- The Google Maps/Firebase browser keys in the Blade views and
  `public/firebase-messaging-sw.js` are public by design. Make sure they are
  restricted to the `seemacabsgoa.com` HTTP referrer in Google Cloud Console.
