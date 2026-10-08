# Deploying Seema Cabs Goa on Coolify

One Docker image serves everything on a single domain:

| URL | Served by |
|---|---|
| `/`, `/airport-taxi-goa.html`, `/contact.html`, … | Static marketing site (repo root) |
| `/app/...` | Customer PWA (Laravel) |
| `/login`, `/dashboard`, … | Admin panel (Laravel, 2FA protected) |
| `/api/...` | REST API for the mobile apps (Laravel) |
| `/storage/...` | Uploaded images (persistent volume) |

The image is built from [`taxigo/Dockerfile`](Dockerfile) on top of
`serversideup/php:8.2-fpm-nginx` (nginx + PHP-FPM, listens on port **8080**).
The Docker build context is the **repository root**, because the marketing pages
live there and get copied into Laravel's `public/` directory during the build.

---

## 1. Before you start

- **Database access.** The app uses the existing MySQL database. Allow inbound
  connections on port 3306 from the Coolify server's public IP in the database
  host's firewall.
- **Frontend assets.** `taxigo/public/build` is committed and used as-is, so the
  server doesn't need Node. After changing anything in `resources/`, run
  `npm run build` inside `taxigo/` and commit the updated `public/build`.
- **Mobile apps.** Check which API base URL the Android and iOS apps use. If it
  is `https://seemacabsgoa.com/taxigo/api/...`, the apps must be updated to
  `https://seemacabsgoa.com/api/...` before DNS points at Coolify. Until then,
  keep the old server answering the old URL.
- **Collect from the current server:**
  - the current `APP_KEY`. Reuse it: a new key logs everyone out and breaks any
    encrypted values.
  - the database and mail credentials
  - `storage/app/firebase/firebase_credentials.json`
  - the uploaded images (`storage/app/public/`, ~150 MB)

## 2. Create the application in Coolify

1. **+ New → Application → Private Repository (with GitHub App)** and select
   `ofinit/SeemaCabsGoa`, branch `main`.
2. **Build Pack:** `Dockerfile`.
3. **Base Directory:** `/`
4. **Dockerfile Location:** `/taxigo/Dockerfile`
5. **Ports Exposes:** `8080`
6. **Domains:** `https://seemacabsgoa.com,https://www.seemacabsgoa.com`
   (Coolify issues the TLS certificates).
7. **Health check** (optional): path `/healthcheck`, port `8080`.

## 3. Environment variables

Add these under **Environment Variables**. Never commit a `.env` file.

| Variable | Value |
|---|---|
| `APP_NAME` | `Seema Cabs Goa` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(the existing key from the current server)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://seemacabsgoa.com` |
| `LOG_LEVEL` | `error` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` | *(database host)* / `3306` |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | *(database credentials)* |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | *(SMTP settings)* |
| `SESSION_DRIVER` | `file` |
| `SESSION_SECURE_COOKIE` | `true` |
| `CACHE_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `local` |

The Razorpay and Cashfree keys are **not** environment variables. They live in
the `environments` database table and are managed from the admin panel.

## 4. Persistent storage

Under **Persistent Storage**:

| Type | Destination path in container | Purpose |
|---|---|---|
| Volume | `/var/www/html/storage/app/public` | Uploaded images: driver documents, cab photos, banners. **Required**; without it, uploads are lost on every deploy. |
| Volume | `/var/www/html/storage/framework/sessions` | Optional. Keeps admin and customer sessions alive across deploys. |
| File | `/var/www/html/storage/app/firebase/firebase_credentials.json` | Paste the Firebase service-account JSON. Required for push notifications. |

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

## 5. Deploy

Click **Deploy**. When the container starts it automatically runs
`storage:link` and caches the config, routes, views and events.

**Migrations do not run automatically**, because the database is the live
production database. When a release includes new migrations, back up the
database first, then run this in the Coolify **Terminal** for the app:

```bash
php artisan migrate --force
```

## 6. Verify

- [ ] `https://seemacabsgoa.com/` shows the marketing homepage
- [ ] `/airport-taxi-goa.html` and the other marketing pages load with styles and images
- [ ] `/app` loads the customer PWA; logging in with OTP works
- [ ] `/login` shows the admin login; 2FA works
- [ ] An existing uploaded image (e.g. a cab photo in the admin panel) displays
- [ ] A test booking and payment (Razorpay/Cashfree) go through
- [ ] A push notification arrives (Firebase credentials mounted)
- [ ] The mobile apps can reach `/api/...`
- [ ] Coolify **Logs** show no errors (Laravel logs go to stderr)

## 7. Updating

Push to `main`, then click **Redeploy**, or enable Coolify's auto-deploy webhook.
The uploads volume, sessions volume and Firebase file are kept between deploys.

## Security notes

- `routes/web.php` exposes unauthenticated `GET /clear`, `/migrate` and
  `/in-city-rides` routes that run Artisan commands. Anyone can trigger a
  migration against the live database. Remove them or put them behind admin
  auth before going live.
- The Google Maps/Firebase browser keys in the Blade views and
  `public/firebase-messaging-sw.js` are public by design. Make sure they are
  restricted to the `seemacabsgoa.com` HTTP referrer in Google Cloud Console.
