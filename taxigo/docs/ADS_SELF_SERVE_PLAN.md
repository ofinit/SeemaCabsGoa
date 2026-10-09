# Self-Serve Advertising Platform — Plan

Status: **proposal, not implemented** (revision 2). Owner: OfinIT Solutions Pvt. Ltd.

Goal: let businesses buy, upload and manage ads inside the Seema Cabs Goa
customer app (PWA + Android/iOS) and website **by themselves**, see full
analytics, and pay through **Seema Holidays' payment gateway**. Every creative
is cropped to the placement's shape, compressed and converted to WebP.

> **Revision 2 — payment model changed.** Ad payments are collected by the
> fleet operator (**Seema Holidays**) on its own gateway. Seema Holidays keeps
> a **10% commission**; the rest goes to **OfinIT, inclusive of GST**, split
> out automatically (same Razorpay Route / Cashfree Easy Split mechanism as the
> ride platform fee). See §0 for the money flow and §6 for the details; §2,
> §4, §9, §11 and §12 are adjusted accordingly.

---

## 0. Revised money flow (revision 2)

**Recommended legal structure:** Seema Holidays is the **seller of ad space**
in its own app and website, and OfinIT supplies Seema Holidays an
**ad-platform and ad-operations service** for a revenue share. Each party
invoices only its own supply, the money lands with the party that sold to the
advertiser, and the gateway split is the normal marketplace feature.

Example: an ad sold for **₹1,000 + 18% GST**:

| Step | Amount |
|---|---|
| Advertiser pays Seema Holidays (Seema's PG) | **₹1,180** (₹1,000 + ₹180 GST) |
| OfinIT's share: 90% of ₹1,000 = ₹900, **+ 18% GST ₹162** | **₹1,062** → auto-split to OfinIT |
| Seema Holidays keeps | **₹118** = ₹100 commission (10%) + ₹18 |
| Seema's GST: pays ₹180 output GST, claims ₹162 input credit on OfinIT's invoice | net **₹18** to the government (the ₹18 it kept) **[CA]** |

So Seema Holidays nets exactly its **10% (₹100)**, and OfinIT receives its
**90% inclusive of GST (₹1,062)**.

**Why not "OfinIT sells the ad, Seema just collects":** collecting money in
your own merchant account for another company's sale is a payment-aggregation
activity (RBI PA rules), conflicts with the gateway's merchant-of-record and
KYC terms, and needs "pure agent" GST treatment. Selling the ad space itself
and paying OfinIT through the gateway's split feature avoids all three.
**[CA]** to confirm the structure, SAC codes and input-credit eligibility
(Seema's rides use the 5% no-ITC rate; this ad supply is separate, at 18%).

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

> **Revision 2: build the ads module inside the existing Seema Laravel app**,
> at `www.seemacabsgoa.com/advertise` (advertiser portal) with the admin side
> in the existing admin panel. Reasons the original separate-service design no
> longer fits:
> - Checkout must use **Seema's gateway and domain**, and the money, invoices
>   and reconciliation belong in **Seema's database**, next to ride payments.
> - Everything needed already exists there: gateway order creation and
>   server-side verification, `PlatformFeeTransferService` (Route / Easy Split
>   to OfinIT, with refund reversal), `InvoiceService` with business profiles,
>   GST and audited settings, the WebP image helper, and the ad tables.
> - It removes a second app, database, domain and login system.
>
> To keep the multi-operator option open for other TaxiGo operators, the
> module is written **tenant-ready**: its own `ad_*` tables, its own services,
> and no assumptions about a single operator, so it can be extracted into a
> shared "OfinIT Ads" service later. Each operator would then use its own
> gateway + OfinIT split account, exactly as here.
>
> The rest of this section describes the original (revision 1) design and is
> kept for reference only.

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

## 4. Advertiser self-serve journey (www.seemacabsgoa.com/advertise — rev 2)

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
   6. **Review & pay**: itemised quote with 18% GST, coupon code, then checkout on
      Seema Holidays' gateway with OfinIT's share split out automatically (§6).
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

## 6. Payments: collected by Seema Holidays, OfinIT's share split out (revision 2)

**Gateway & checkout**
- Checkout uses **Seema Holidays' own** Razorpay / Cashfree account (the same
  keys as rides), so it must run on a **Seema domain** that is whitelisted in
  that account: `www.seemacabsgoa.com/advertise` (recommended) or a white-label
  `ads.seemacabsgoa.com`. It can no longer be `ads.ofinit.com`.
- **Tell the gateways** that Seema Holidays also sells advertising (business
  category / product listing on the website, ad pricing page, ad T&C and refund
  policy). Selling an undeclared category can get a merchant account flagged.
- Same safeguards as rides: server-side order creation with the amount from the
  stored campaign order, `PaymentVerifier`-style confirmation with the gateway,
  signed webhooks, idempotency, nightly reconciliation.

**Split to OfinIT** (reuse `PlatformFeeTransferService`)
- **Razorpay:** Route transfer of OfinIT's share (₹1,062 in the example) to
  OfinIT's linked account in Seema's Razorpay (`acc_Qm5h0HughTNOA3`).
- **Cashfree:** `order_splits` to OfinIT's vendor id at order creation.
- New settings: **"Ad commission — Seema Holidays (%)"** = 10 (editable,
  audited); OfinIT gets the remainder **+ GST**. Ad GST rate 18% and SAC
  **[CA]**. Each order snapshots the % and amounts.
- **Refunds** (rejected ad, cancelled campaign): Razorpay `reverse_all` /
  Cashfree `refund_splits` reverse OfinIT's share with the refund; pro-rata
  partial refunds reverse pro-rata. Credit notes on both sides.
- **Offline / admin-entered ad sales** (cash, bank transfer, existing admin
  ad module): no gateway split. OfinIT's share appears on its monthly invoice
  to Seema Holidays as **payable**, settled by bank transfer.

**Invoices** (reuse `InvoiceService` + business profiles)
- **Seema Holidays → advertiser:** tax invoice for the ad, 18% GST (CGST+SGST
  in Goa, IGST for other states), advertiser GSTIN for B2B input credit. New
  series, e.g. `SH/26-27/AD00001`. Issued on payment (ads are paid in advance;
  if a campaign starts later, a receipt voucher first and the invoice at
  start **[CA]**).
- **OfinIT → Seema Holidays:** a separate **"Ad platform & operations"** line
  (own SAC) on the existing monthly OfinIT invoice, or its own monthly invoice:
  90% of net ad revenue + 18% GST. Marked as already collected via split
  (online sales) or payable (offline sales).
- **TDS:** Seema paying OfinIT may require TDS deduction (194C/194J
  depending on how the service is classified) **[CA]**. If TDS applies, the
  split must transfer the share **net of TDS** and the TDS is shown on the
  invoice settlement.

**Data:** ad payment and invoice data now live in **Seema's** database, next
to ride payments (same gateway account, same reconciliation). OfinIT sees its
share through the transfers report and its invoices.

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
| **1. MVP in the Seema app (rev 2)** | Placements; advertiser signup; campaign wizard (placements, dates, area); crop → WebP pipeline; checkout on Seema's gateway with OfinIT split (reusing `PlatformFeeTransferService`) + refund reversal; Seema → advertiser GST invoice and OfinIT's monthly ad-share line (reusing `InvoiceService`); ad commission setting; review queue; viewable impressions/clicks; basic dashboard; migrate existing 20 ads and 8 screens | ~4–5 weeks (less than rev 1: payments, splits, invoices and settings already exist) |
| **2. Growth** | Hour/day targeting, availability calendar, share-of-voice pricing, website placements (P9), email footer (P10), PDF/scheduled reports, coupons, A/B creatives, refunds automation, tenant read-only view, invalid-traffic filtering v2 | ~4–6 weeks |
| **3. Scale** | New native placements (app release), sponsored push (P11), conversion pixel/postbacks, wallet and agency accounts, onboarding of other TaxiGo operators, CPM/CPC pricing, object storage + CDN, ClickHouse if needed | ongoing |

---

## 12. Open questions (need a decision)

1. ~~Revenue share~~ — **decided (rev 2):** Seema Holidays 10%, OfinIT the
   rest inclusive of GST, split automatically. Is the 10% on the ad price
   **excluding** GST (assumed), and the same for every placement?
2. **Pricing model** at launch: keep per-day per-placement (today's model), or
   introduce CPM packages from day one?
3. ~~Which OfinIT gateway~~ — **decided (rev 2):** Seema Holidays' Razorpay /
   Cashfree. Has Seema informed both gateways that it will also sell
   advertising on `seemacabsgoa.com`?
3a. **[CA]** Confirm the structure in §0 (Seema sells the ad space; OfinIT
   supplies an ad-platform service to Seema), the SAC codes, Seema's input
   credit on OfinIT's 18% invoice, and whether Seema must deduct TDS.
4. Should **gender targeting** stay? It exists today. Recommendation: drop it,
   as it adds privacy risk and little value for local Goa advertisers.
5. **Who reviews ads**, and what review SLA is promised?
6. Will the **Android/iOS apps** get an update in Phase 1, or only via the
   unchanged proxy API until Phase 3?
7. ~~`ads.ofinit.com`~~ — **rev 2:** `www.seemacabsgoa.com/advertise`
   (or `ads.seemacabsgoa.com`), whitelisted in Seema's gateways.
