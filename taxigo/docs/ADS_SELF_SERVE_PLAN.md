# Self-Serve Advertising Platform — Plan

Status: **proposal, not implemented**. Owner: OfinIT Solutions Pvt. Ltd.

Goal: let businesses buy, upload and manage ads inside the Seema Cabs Goa
customer app (PWA + Android/iOS) and website **by themselves**, see full
analytics, and pay **OfinIT** directly. Every creative is cropped to the
placement's shape, compressed and converted to WebP.

---

## 1. What exists today

| Area | Current state | Gap |
|---|---|---|
| Data | `advertisements` (screens JSON, banner, targeting by gender/state/location, dates, billing fields, amount), `advertisers` (lead details), `screen_prices` (8 screens, price per day), `advertisement_user_clicks` (click + user + lat/long) | No campaigns, orders, invoices, creatives per placement, or impressions |
| Creation | Admin only (`Admin\AdvertisementController@storeUpdate`); image goes through `UploadImageWebpConversion` (WebP, no crop) | No advertiser self-serve, no cropping, one image for every screen |
| Serving | `GET /api/advertisement-list` (mobile + PWA via proxy) splits ads into "top" and "bottom" lists | Ignores `status` (no approval gate); ignores `start_time`/`end_time`; per-screen choice mostly unused (only screen ids 7/8 matter) |
| Placements | 8 screen prices in DB; PWA shows ads on Home, Finding-taxi and Advertise pages | Screen 3 and 8 titles and slugs are swapped (`…Top Section` ↔ `…bottom-section`) |
| Tracking | Clicks only (141 rows), stored with user id and GPS | No impressions, no reach, no CTR, no dedup or fraud filtering; per-user GPS is more data than needed |
| Payments | `payment_method` / `total_amount` text fields; no gateway | Requirement: collect via **OfinIT's** gateway on the **ofinit.com** domain |
| Analytics | None for advertisers | Need dashboards, exports, reports |

All 20 existing ads, 8 screens and 141 clicks must be migrated, not lost.

---

## 2. Recommended architecture

**Build "OfinIT Ads" as a separate, multi-tenant service at `ads.ofinit.com`**,
deployed as its own app on the same Coolify server, with its own database.
Seema Cabs Goa becomes its first *tenant*.

Why separate rather than inside the Seema app:

- **Payments must be OfinIT's.** The gateway account, KYC website, GST invoices
  and webhooks all have to sit on an ofinit.com domain under OfinIT's legal
  entity. Seema's own Razorpay/Cashfree keys (in its `environments` table)
  must never touch ad money.
- **Data ownership.** Advertiser accounts, invoices and revenue belong to
  OfinIT, not to the cab operator's database.
- **Reuse.** TaxiGo is sold to other operators (`taxigo.software`). One ads
  platform can serve every TaxiGo tenant, website or app with no copy-paste.

```
 Advertiser ──► ads.ofinit.com  (portal, checkout, invoices, analytics)
                    │   OfinIT Razorpay/Cashfree account + webhooks
                    │
                    ├── Ad Serving API  /v1/serve      (decides which ad)
                    ├── Event API       /v1/events     (impressions, views)
                    └── Click redirect  /c/{token}     (click, then 302)
                              ▲
 Seema PWA / website ─────────┤  direct (JS snippet, cached responses)
 Seema mobile apps ──► Seema API /api/advertisement-list (thin proxy, cached)
```

- The Seema Laravel app keeps `/api/advertisement-list` and
  `/api/advertisement-click` **with the same response shape** but proxies them
  server-to-server to the ad server (cached 60 s). The existing mobile apps keep
  working with **no app release**. New placements in native apps need an
  update later.
- The PWA and marketing site call the ad server directly through a small
  `ofinit-ads.js` snippet (lazy-loaded, ~5 KB, no third-party trackers).
- Tenant isolation: every row carries `tenant_id`, and API keys are scoped per
  tenant and per platform.

---

## 3. Placements (inventory)

Shape is fixed per placement. The advertiser crops once per shape, and the
system generates all sizes.

| # | Placement | Surface | Shape (ratio · master px) | Notes |
|---|---|---|---|---|
| P1 | Home hero carousel | PWA, apps | 2:1 · 1600×800 | Premium; max 5 rotating; auto-advance 5 s |
| P2 | Home inline card | PWA, apps | 3:1 · 1500×500 | Between service tiles |
| P3 | Finding-a-taxi (top) | PWA, apps | 4:5 · 1080×1350 | **Highest dwell time**, so premium price |
| P4 | Finding-a-taxi (bottom) | PWA, apps | 3:1 · 1500×500 | |
| P5 | Booking confirmed | PWA, apps | 2:1 · 1600×800 | Good for local offers near pickup or drop |
| P6 | Driver details / trip in progress | PWA, apps | 3:1 · 1500×500 | Small; never covers the map, driver info or SOS |
| P7 | Ride complete / rating | PWA, apps | 1:1 · 1080×1080 | After the rating is submitted, not before |
| P8 | Rides history & Account | PWA, apps | 3:1 · 1500×500 | Low price, high frequency |
| P9 | Website landing pages (airport, route, sightseeing) | seemacabsgoa.com | 6:5 · 1200×1000 sidebar, 8:1 · 1600×200 strip | Contextual: e.g. Baga hotels on `panjim-to-baga-taxi` |
| P10 | Booking email / invoice footer | Email | 4:1 · 1200×300 | Static image + tracked link |
| P11 | Sponsored push (later) | Apps | Text + 2:1 image | Opt-in users only; max 1/week; strict review |

**No-ad zones, enforced in code:** OTP/login, payment and checkout steps, SOS
and safety screens, the driver app, and error pages.

Mapping from today's 8 screens: 1→P1+P2, 2→P2, 3/8→P3/P4 (fix the swap),
4→P5, 5→P6, 6→P7, 7→P8.

---

## 4. Advertiser self-serve journey (ads.ofinit.com)

1. **Sign up / log in.** Email or phone OTP, or Google. Optional team members (owner, editor, viewer).
2. **Business profile.** Legal name, GSTIN (optional; validated, and enables an
   ITC-ready invoice), PAN, billing address, category. Accepting the T&C and ad
   policy is mandatory.
3. **Create campaign** (wizard, autosaved as a draft):
   1. **Goal**: website visits / calls / WhatsApp / directions / app install.
      This sets the CTA button and how a click is tracked.
   2. **Placements**: pick on a visual phone mock-up showing exactly where the
      ad appears; live price per placement.
   3. **Targeting**: area (North Goa, South Goa, or specific towns, matched on
      the rider's pickup/drop area; no personal data), platform (PWA / Android /
      iOS / website), days of week and hours, language later.
   4. **Schedule & budget**: date range, **availability calendar** (sold-out
      dates greyed out), share of voice (e.g. 1 of 5 rotation slots).
   5. **Creatives**: upload and crop per placement shape (section 5), headline
      and CTA text where the placement supports it, landing URL or phone /
      WhatsApp number. Live preview in the mock-up.
   6. **Review & pay**: itemised quote with 18% GST, coupon code, then OfinIT
      checkout (section 6).
4. **Moderation.** Paid campaigns go to review (SLA: 24 h). If rejected: edit
   and resubmit, or get an automatic full refund.
5. **Live.** Pause or resume, swap creative (re-review), extend dates (pay the
   difference), duplicate the campaign, renew in one click.
6. **After.** Final report emailed; invoice and credit notes downloadable any
   time.

Campaign states: `draft → pending_payment → in_review → approved → scheduled →
live ⇄ paused → completed`, plus `rejected`, `refunded` and `cancelled`.
Every transition is written to an audit log.

---

## 5. Creative pipeline: crop, compress, WebP

**In the browser (portal):**
- Cropper.js locked to the placement's aspect ratio, with zoom, pan and rotate.
- Reject below-minimum resolution before upload, e.g. master width ≥ 1080 px.
- Upload the **original** plus **crop box data** (x, y, w, h, rotation), not a
  pre-cropped image. That way the server crops consistently, and the
  advertiser can re-crop later without re-uploading.
- Optional client-side downscale of very large photos (> 4000 px) to speed up
  mobile uploads.

**On the server (queued job):**
1. Validate: real MIME via `finfo` (JPEG/PNG/WebP only; no SVG, no animated
   GIF), max 10 MB, max 8000×8000, decompression-bomb guard.
2. Auto-orient from EXIF, then **strip all metadata** (EXIF/GPS).
3. Apply the crop box, then resize to the master size and responsive variants
   (1×, 2×, plus a 480 px thumbnail).
4. Encode to **WebP** (quality 80, method 6). Target ≤ 150 KB for the master
   and ≤ 60 KB for mobile variants; step quality down automatically until it
   fits, with 65 as the floor.
5. Store with content-hashed filenames (`/{tenant}/{creative}/{hash}-1600.webp`)
   and serve with `Cache-Control: public, max-age=31536000, immutable`.
   Originals stay **private**.
6. Generate a low-quality placeholder colour or blur for smooth loading.

Reuse the existing Intervention Image setup (`UploadImageWebpConversion`), and
check that the server's GD build has WebP support. Later, add object storage
(S3/R2) plus a CDN; until then, a persistent volume on the ads app.

---

## 6. Payments: collected by OfinIT only

- Checkout runs only on `ads.ofinit.com` with **OfinIT's own** Razorpay or
  Cashfree merchant account; that domain is the one registered in the
  gateway's KYC. Card, UPI, netbanking and wallets.
- Server-side order creation → hosted or modal checkout → **signature-verified
  webhook** marks the order paid (never trust the browser redirect).
  Idempotent webhook handling and a nightly reconciliation job against the
  gateway's settlement report.
- **GST-compliant invoices** issued by OfinIT: sequential numbering per
  financial year, OfinIT GSTIN, advertiser GSTIN if given, SAC 998365
  (advertising space sale; confirm with your CA), CGST/SGST for Goa, IGST for
  other states.
- Refunds go through the gateway API with a **credit note**. They are automatic
  when an ad is rejected; pro-rata if OfinIT cancels.
- Also: payment links for sales-assisted deals, coupons and discounts.
  Later: prepaid wallet credits and invoicing on net terms for agencies.
- **Revenue share with the cab operator** (if any) is calculated per tenant from
  delivered campaigns and paid out by OfinIT (Razorpay Route or a monthly
  transfer). Business decision: see open questions.
- Seema's app and database store **no** ad payment data; they only see
  approved creatives.

---

## 7. Serving, tracking & analytics

### Serving rules
- Eligible = approved, paid, inside date and time window, targeting matches,
  placement matches, frequency cap not reached.
- Rotation: weighted by purchased share of voice; ties broken randomly.
  Fallback house ads (OfinIT "Advertise here") fill empty slots.
- **Frequency cap**: same ad max 3 views per user per placement per day.
- Responses are cached per tenant + placement + area for 60 s.

### Events

| Event | Definition |
|---|---|
| Served | Ad returned by `/v1/serve` |
| **Impression (viewable)** | ≥ 50% of the ad visible for ≥ 1 s (IntersectionObserver in the PWA and website, the platform equivalent in native apps) |
| Click | Via `/c/{signed-token}` → 302 to landing URL with UTM tags (`utm_source=seemacabsgoa&utm_medium=app&utm_campaign={id}`) |
| Call / WhatsApp / Directions | CTA-specific click types |
| Conversion (optional) | Advertiser adds a tiny pixel or uses a coupon code; postback API later |

Events are batched with `navigator.sendBeacon` and deduplicated by
`(event_id)`. They're signed with a short-lived token from `/v1/serve`, so
nobody can fake impressions.

**Invalid-traffic filtering:** drop known bots and headless agents, cap
repeated clicks from the same device (1 per ad per 30 min), rate-limit by IP,
and exclude internal/test users. Filtered events are kept but flagged, never
billed or shown.

### Advertiser dashboard
- Overview: impressions, **reach** (unique devices), frequency, clicks, CTR,
  calls and WhatsApp taps, spend, eCPM, eCPC.
- Breakdowns: by day and hour, placement, area (North/South Goa, town),
  platform, creative (for A/B), weekday.
- Pacing bar: delivered vs expected impressions so far.
- Exports: CSV and PDF report. Scheduled weekly email plus a final campaign
  report.
- **Privacy floor:** never show breakdowns covering fewer than 10 unique users.
  Advertisers never see personal data (no names, phones or exact GPS).

### OfinIT admin / tenant dashboards
- Inventory fill rate and sell-through per placement and day, revenue
  (gross, GST, net, refunds), top advertisers, review queue with SLA timer,
  invalid-traffic rate, payout ledger per tenant.
- The cab operator gets a **read-only** view: which ads run in their app, the
  revenue share, and the ability to block categories or specific advertisers.

### Data pipeline
- Raw `ad_events` table (append-only, partitioned by month, 13-month
  retention), then **hourly rollups** into `ad_stats_hourly` and daily rollups
  into `ad_stats_daily` via a scheduled job. Dashboards read rollups only.
- MySQL handles the current scale (about 2.5k users). Move raw events to
  ClickHouse only if volume grows past ~5M events/month.

---

## 8. Content policy & moderation (best practices)

- Follow the **ASCI code**. In India, ban: alcohol (including surrogate ads),
  tobacco and vaping, betting and online gambling, adult content, weapons,
  unverified medical or financial claims, political ads, crypto schemes.
  Restricted (manual approval and licence proof): casinos (legal in Goa, but
  rules apply), real estate (RERA number shown), loans (RBI-registered lender
  name).
- Automated checks before human review: landing URL reachable over HTTPS, not
  on Google Safe Browsing's list, not a URL shortener; image NSFW check;
  text-heavy warning; profanity filter.
- Every ad shows a **"Sponsored"** label and a "Why this ad / Report ad" menu.
  Users can report an ad, and 3 reports auto-pause it pending review.
- Reviewer actions: approve, reject with reason codes (shown to the advertiser),
  request changes. Two-person approval for the restricted categories.

---

## 9. Data model (ads.ofinit.com)

`tenants`, `tenant_api_keys`, `placements` (tenant, key, surface, ratio,
sizes, base price/day, max slots, active), `advertisers`, `advertiser_users`,
`campaigns` (goal, status, dates, hours, budget, share of voice),
`campaign_placements`, `campaign_targets` (area/platform/day/hour),
`creatives` (original path, crop box, status), `creative_variants` (size,
path, bytes), `orders`, `payments`, `refunds`, `invoices`, `credit_notes`,
`coupons`, `ad_events`, `ad_stats_hourly`, `ad_stats_daily`,
`moderation_reviews`, `reports` (user ad reports), `payouts`, `audit_logs`.

---

## 10. Security & operations

- Ads app: separate Coolify app, separate database, its own backups, its own
  secrets. Gateway keys only as runtime env vars, never in git.
- Advertiser auth: rate-limited OTP, optional 2FA, session timeout. Admin and
  reviewer routes behind 2FA (same principle as `TwoFaSecurity`).
- Signed serve and click tokens (HMAC, 15-minute TTL). Open-redirect protection
  on `/c/` (only redirect to the stored landing URL).
- Queue worker plus scheduler needed (Redis + `queue:work` + `schedule:run` as
  Coolify scheduled tasks). The Seema app today runs `QUEUE_CONNECTION=sync`
  and has no scheduler.
- Monitoring: failed webhooks, job failures, the event-ingest error rate, and
  review SLA breaches alert the ad ops email/WhatsApp.

---

## 11. Phased delivery

| Phase | Scope | Rough size |
|---|---|---|
| **0. Quick fixes in Seema app** | Gate ads on `status`; enforce `start_time`/`end_time`; serve ads per screen instead of top/bottom only; fix screen 3/8 swap; add a "Sponsored" label; stop storing per-click GPS; add viewable-impression tracking | ~1 week |
| **1. MVP on ads.ofinit.com** | Tenant + placements; advertiser signup; campaign wizard (placements, dates, area); crop → WebP pipeline; OfinIT checkout + webhook + GST invoice; review queue; serve/events/click APIs; Seema proxy; basic dashboard (impressions, reach, clicks, CTR by day/placement); migrate existing 20 ads and 8 screens | ~5–7 weeks |
| **2. Growth** | Hour/day targeting, availability calendar, share-of-voice pricing, website placements (P9), email footer (P10), PDF/scheduled reports, coupons, A/B creatives, refunds automation, tenant read-only view, invalid-traffic filtering v2 | ~4–6 weeks |
| **3. Scale** | New native placements (app release), sponsored push (P11), conversion pixel/postbacks, wallet and agency accounts, onboarding of other TaxiGo operators, CPM/CPC pricing, object storage + CDN, ClickHouse if needed | ongoing |

---

## 12. Open questions (need a decision)

1. **Revenue share** with the cab operator: none, fixed %, or per placement?
2. **Pricing model** at launch: keep per-day per-placement (today's model), or
   introduce CPM packages from day one?
3. **Which OfinIT gateway** collects ad payments: Razorpay or Cashfree? Is the
   ofinit.com domain already KYC-approved on it?
4. Should **gender targeting** stay? It exists today. Recommendation: drop it,
   as it adds privacy risk and little value for local Goa advertisers.
5. **Who reviews ads**, and what review SLA is promised?
6. Will the **Android/iOS apps** get an update in Phase 1, or only via the
   unchanged proxy API until Phase 3?
7. Is `ads.ofinit.com` the final domain (DNS + TLS on the Coolify server)?
