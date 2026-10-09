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
| P3 | Finding-a-taxi — top banner | PWA, apps | 3:1 · 1500×500 | Above the search progress; up to 3 rotating |
| P4 | Finding-a-taxi — bottom banner | PWA, apps | 3:1 · 1500×500 | Below the progress; up to 3 rotating |
| P12 | Finding-a-taxi — **large card (double size)** | PWA, apps | 4:5 · 1080×1350 | Replaces the top banner area when sold; up to 2 rotating; **highest dwell time** |
| P13 | Finding-a-taxi — **full-screen takeover** | PWA, apps | 9:16 · 1080×1920 | **Exclusive: 1 advertiser per day.** Shown once per search while the system looks for a cab; close button after 3 s; closes itself the moment a cab is found; then the screen shows P12/P3 + P4 as usual |
| P5 | Booking confirmed | PWA, apps | 2:1 · 1600×800 | Good for local offers near pickup or drop |
| P6 | Driver details / trip in progress | PWA, apps | 3:1 · 1500×500 | Small; never covers the map, driver info or SOS |
| P7 | Ride complete / rating | PWA, apps | 1:1 · 1080×1080 | After the rating is submitted, not before |
| P8 | Rides history & Account | PWA, apps | 3:1 · 1500×500 | Low price, high frequency |
| P9 | Website landing pages (airport, route, sightseeing) | seemacabsgoa.com | 6:5 · 1200×1000 sidebar, 8:1 · 1600×200 strip | Contextual: e.g. Baga hotels on `panjim-to-baga-taxi` |
| P10 | Booking email / invoice footer | Email | 4:1 · 1200×300 | Static image + tracked link |
| P11 | Sponsored push (later) | Apps | Text + 2:1 image | Opt-in users only; max 1/week; strict review |
| P14 | **Sightseeing package — sponsored stop** | PWA, apps, website | 3:1 · 1500×500 | On a package's detail page, e.g. a restaurant or spice farm on the North Goa tour route; contextual to that package |
| P15 | **Airport arrival offers** | PWA, apps | 2:1 · 1600×800 | Booking-confirmed screen **for airport pickups only**: "Welcome to Goa" offers (hotels, scooter rentals, SIM cards, restaurants). High-intent tourists |
| P16 | **App-open sponsor** | PWA, apps | 1:1 logo + one line | "Presented by …" on the splash screen for 1.5 s; **exclusive**, sold by the week or month |
| P17 | Notifications screen card | PWA, apps | 3:1 · 1500×500 | Inline between notifications; low price |
| P18 | **In-cab QR card** (offline) | Printed card in the cab | A6 card with a QR code | Seat-back or headrest card; the QR is a tracked `/ads/c/…` link, so scans show in the dashboard. Sold **per cab per month**; Seema prints and places the cards |

**Finding-a-taxi screen rules:** at most one full-screen takeover per search;
banners are hidden while it is open and count impressions only once visible;
nothing ever covers the "Cab found" result or the booking details.

**Not recommended** (kept ad-free): search results list (could be confused
with real cab options), payment and OTP steps, trip-in-progress map, SOS, tax
invoices and receipts, and the driver app.

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

## 4A. PWA submission, admin approval, slots, pricing & expiry (rev 2 scope)

Builds on the existing admin ad module (admin-created ads, active / expired
lists with renew, `screen_prices` per screen per day) and replaces the static
"pending ads" mock-up.

### Advertisers submit ads from the PWA
- **Entry:** the PWA **Advertise** page (`/app/advertise`) gets **"Create an
  ad"**; the WhatsApp contact stays as "Need help?". Any logged-in customer can
  advertise; on the first ad they add business details (business name,
  contact, optional GSTIN + billing address), stored as their advertiser
  profile and reused.
- **Mobile-first wizard:**
  1. **Placement(s):** cards with a screenshot of where the ad appears, its
     shape, price per day and availability.
  2. **Dates:** calendar with sold-out days greyed out; minimum 1 day.
  3. **Creative:** upload, then crop to the placement's shape (§5); one crop per
     selected placement. Link type: website / WhatsApp / call.
  4. **Review & pay:** placements × days, any discount, **GST 18%** (§6A),
     total → Seema Holidays' gateway with OfinIT's split (§6). The selected
     slots are **held for 15 minutes** while paying.
  5. Status becomes **In review**; the advertiser gets a push + email.
- **My Ads** (PWA): each ad with status (In review → Approved / Scheduled →
  Live → Expired, or Rejected with reason + refund status), impressions,
  clicks, CTR, invoice download, **Renew / Extend**, and **Edit & resubmit**
  after a rejection (no new payment).

### Admin approval (mandatory)
- **Admin → Advertisements → Pending approval:** creative previewed inside the
  real placement frame, advertiser and business details, landing link (checked
  for HTTPS and reachability), dates, amount paid.
- **Approve** → scheduled or live, depending on its dates. **Reject** with a
  reason code (shown to the advertiser) → **automatic full refund** with
  OfinIT's split reversed, credit notes, notification. **Request changes** →
  advertiser edits and resubmits without paying again.
- **Nothing is served until it is paid *and* approved** (fixes today's
  behaviour of ignoring `status`). Admin-created ads for offline sales go
  through the same statuses, flagged "offline".
- **Review SLA 24 h.** If approval comes after the start date, the end date
  moves forward by the delay, so the advertiser still gets every paid day.
- Every approval / rejection is audit-logged with the admin's name.

### Slots & placement management
- **Admin → Advertisements → Placements** (extends `screen_prices`): name,
  screen, image shape (ratio + minimum size), **price per day**, **slots**
  (maximum ads shown in rotation at once, e.g. 5), platforms, active on/off,
  sample screenshot. Fixes the swapped screen 3 / 8 titles.
- **Availability:** a day is sold out for a placement when approved + in-review
  + held ads reach its slots. Sold-out days can't be booked; admins see a
  per-placement calendar of bookings.
- **Rotation:** ads in a placement share impressions equally; an OfinIT /
  Seema "Advertise here" house ad fills empty slots.

### Pricing
- **Price per placement per day** (admin-set), with optional **peak-season
  multipliers** (e.g. Dec 15 – Jan 5 × 1.5) and **multi-day discounts** (e.g.
  7+ days −10%, 30+ days −20%), plus coupon codes.
- **GST 18% added on top** (§6A). Seema Holidays' **10% commission is on the
  price after discount, before GST**; OfinIT gets the rest + GST (§0, §6).
- Every order **snapshots** prices, discounts, GST and the commission %, so
  later price changes never alter paid orders or invoices.

### Suggested launch prices (per day, before 18% GST)

The audience is small today, so prices must be low enough for a local shop or
restaurant to try, and must be re-set from measured data. From the data (Oct
2026): new customer sign-ups fell from 342 (Jan 2026) to single digits per
month, paid bookings are ~1 per month, recorded ad clicks were 30–50 per month
in late 2025, and 36 ads have earned ₹1,866 in total. The current admin
prices are also out of line with visibility (the Account screen is the most
expensive at ₹100/day; Home is the cheapest at ₹10/day).

| Placement | Visibility | Launch price / day | 7 days (−10%) | 30 days (−20%) |
|---|---|---|---|---|
| P13 Finding-a-taxi — full-screen takeover | Exclusive, whole screen during search | **₹199** | ₹1,254 | ₹4,776 |
| P16 App-open sponsor ("Presented by") | Exclusive, every app open | **₹149** | ₹939 | ₹3,576 |
| P1 Home hero carousel | Every app open | **₹99** | ₹624 | ₹2,376 |
| P12 Finding-a-taxi — large card (double size) | Long dwell while booking | **₹99** | ₹624 | ₹2,376 |
| P15 Airport arrival offers | Tourists just landed (airport pickups) | **₹79** | ₹498 | ₹1,896 |
| P9 Website landing pages (per page group) | SEO visitors (tourists) | **₹79** | ₹498 | ₹1,896 |
| P3 Finding-a-taxi — top banner | Long dwell while booking | **₹49** | ₹309 | ₹1,176 |
| P5 Booking confirmed | Every paying customer | **₹49** | ₹309 | ₹1,176 |
| P14 Sightseeing package — sponsored stop | Tour customers, per package | **₹49** | ₹309 | ₹1,176 |
| P4 Finding-a-taxi — bottom banner | Long dwell while booking | **₹39** | ₹246 | ₹936 |
| P7 Ride complete / rating | After each trip | **₹39** | ₹246 | ₹936 |
| P2 Home inline, P6 Driver details | Medium | **₹29** | ₹183 | ₹696 |
| P8 Rides history & Account, P17 Notifications | Low | **₹19** | ₹120 | ₹456 |
| **Finding-a-taxi bundle:** P12 large + P4 bottom | The whole waiting screen except takeovers | **₹119** (vs ₹138) | ₹750 | ₹2,856 |
| **Booking journey bundle:** P1 + P12 + P5 | Home, search and confirmation | **₹199** (vs ₹247) | ₹1,254 | ₹4,776 |
| **P18 In-cab QR card** (offline) | Every passenger in the cab | **₹299 per cab per month** (printing included) | — | — |

- **Peak season** (e.g. 15 Dec – 5 Jan, Shigmo, long weekends): × 1.5.
- Minimum order **₹199** (before GST), so very small orders don't cost more
  to review than they earn.
- Example: Home hero for 30 days = ₹2,376 + ₹427.68 GST = ₹2,803.68; Seema
  Holidays keeps 10% = ₹237.60; OfinIT gets ₹2,138.40 + GST.
- **Review after 30 days of measured viewable impressions** (Phase 0 adds
  them). Target ≈ ₹100–150 per 1,000 viewable impressions (typical for
  local mobile display); raise or lower each placement's price to match.
  Website placements depend on marketing-site traffic, so check that in
  Google Analytics / Search Console before selling them.

### Targeting
- **Kept:** area (North / South Goa, town of pickup or drop), platform (PWA /
  Android / iOS / website), days of week and hours.
- **Gender targeting: dropped** from self-serve, and the existing field is
  ignored when serving. Reasons: never used (0 of 36 ads), it would split an
  already small audience in half, the stored gender is unreliable (optional,
  self-declared, and the PWA review form pre-selects "Male"), and targeting by
  a personal attribute adds privacy (DPDP) and fairness risk for no benefit to
  local advertisers, who target tourists by place and time.

### Expiry & renewal
- An ad runs from its start date + time to its **end date + time**. An hourly
  scheduled job marks ended ads **Expired** and removes them from serving
  (today, expiry is only a date filter on the list).
- **Reminder 3 days before expiry** (push + email) with a one-tap **Renew**:
  a new order for the following dates, same creative, **no re-approval** unless
  the creative or link changes.
- On expiry the advertiser gets a **final report** (impressions, reach, clicks,
  CTR). Expired ads stay in My Ads and in the admin Expired list (existing).

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
- **TDS: decided — no TDS deduction** on payments to OfinIT; OfinIT shares
  GST invoices for every amount. The split transfers OfinIT's full share.
  (Note for the accountant: TDS is an income-tax rule separate from GST, so
  revisit if annual payments to OfinIT cross the TDS thresholds.)

**Data:** ad payment and invoice data now live in **Seema's** database, next
to ride payments (same gateway account, same reconciliation). OfinIT sees its
share through the transfers report and its invoices.

---

## 6A. GST on ad payments (rev 2)

**Seema Holidays collects GST from the advertiser**, as the seller of the ad
space.

**At checkout**
- Prices are shown **before GST**; **18% is added at checkout** (normal for
  business advertising). Example: ₹1,000 ad + ₹180 GST = **₹1,180**.
- **Which GST** (for advertising, it follows where the buyer is) **[CA]**:

| Advertiser | GST charged |
|---|---|
| Goa business, or individual with a Goa address | CGST 9% + SGST 9% |
| Business registered in another state (GSTIN of another state) | IGST 18% |
| Individual without GSTIN | by their address on record, else Goa |

  (Rides differ: always Goa GST, because the trip starts in Goa.)
- **Optional business GSTIN** at checkout, as for rides: the business gets a
  B2B invoice and can claim the GST back, and the GSTIN's state decides IGST vs
  CGST + SGST.

**Where the GST money goes** (₹1,000 ad)

| | Amount |
|---|---|
| Collected from advertiser | ₹1,180 (₹1,000 + ₹180 GST) |
| Transferred to OfinIT (90% + 18% GST) | ₹1,062 |
| Seema Holidays keeps | ₹118 |
| Seema pays the government: ₹180 output GST − ₹162 credit on OfinIT's invoice | ₹18 |
| **Seema Holidays' real earnings** | **₹100** (its 10%) |

Seema reports ad sales in **GSTR-1** (B2B, B2B inter-state, B2C) and pays in
**GSTR-3B**. Refunds of rejected ads issue a **credit note** that reverses the
GST.

**⚠️ Key risk: input-tax credit.** The 10% / 90% split only works if Seema
Holidays **can offset the ₹162 GST on OfinIT's invoice** against the ₹180 it
owes. Seema's rides use the **5% rate without input credit**, which blocks
credit for the ride business. The ad business is a separate 18% supply, so
credit **should** be allowed for it, but **the CA must confirm**. If it can't:
Seema would pay the full ₹180 from the ₹118 it kept and **lose ₹62 per ₹1,000
ad**. The fallback then is either:
- OfinIT gets 90% + GST via a separate monthly invoice (not the automatic
  split) and Seema keeps the full ₹180 to remit, or
- adjust the percentages so Seema still nets ₹100 after its GST cost.

**Build items**
- **Settings → GST:** ad GST rate (default 18%) and ad SAC code **[CA]**.
- **IGST** at ad checkout, chosen from the advertiser GSTIN's state (the
  invoice engine already supports IGST).
- **GSTR-1 export** extended to ad invoices, including the inter-state B2B
  section.

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
| Click | Via `/ads/c/{signed-token}` → logged → 302 to the landing URL with UTM tags (see "Click tracking & UTM" below) |
| Call / WhatsApp / Directions | CTA-specific click types |
| Conversion (optional) | Advertiser adds a tiny pixel or uses a coupon code; postback API later |

Events are batched with `navigator.sendBeacon` and deduplicated by
`(event_id)`. They're signed with a short-lived token from `/v1/serve`, so
nobody can fake impressions.

### Click tracking & UTM (rev 2)

**Today:** PWA ads are plain links straight to the advertiser's site. **No
UTM tags are added, and PWA clicks are not counted at all** (only the mobile
apps call `/api/advertisement-click`; that is where the 141 recorded clicks
come from). Advertisers therefore can't see Seema traffic in their own
analytics.

**New flow:** every ad links to `www.seemacabsgoa.com/ads/c/{signed-token}`.
The server logs the click (same counting for PWA, apps and website), then
redirects (302) to the landing URL with UTM tags added:

```
utm_source=seemacabsgoa
utm_medium=pwa | android | ios | website
utm_campaign=<ad order id, e.g. AD-00042>
utm_content=<placement, e.g. home_hero>
```

- **The advertiser's own UTMs win:** tags already in their URL are kept, and
  only missing ones are added.
- **Only to the stored landing URL** (no open redirect), HTTPS only.
- **WhatsApp ads** (`wa.me`): UTMs don't apply, so the message is prefilled
  with "Hi, I saw your ad on Seema Cabs Goa" for attribution. **Call ads**
  (`tel:`) are counted as call taps.
- The advertiser dashboard shows our click counts; their Google Analytics
  shows the same visits under `utm_source=seemacabsgoa`.
- **Phase 0 quick fix:** make the PWA call the existing click API and add UTM
  tags, before the full redirect service.

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
   **Decided:** yes, the gateways will be informed.
3a. **[CA]** Confirm the structure in §0 (Seema sells the ad space; OfinIT
   supplies an ad-platform service to Seema), the SAC codes, and Seema's input
   credit on OfinIT's 18% invoice. **TDS: decided — not deducted** (§6).
3b. **Pricing — decided: per-day price.** Launch prices proposed in §4A
   ("Suggested launch prices"); confirm or adjust.
4. ~~Gender targeting~~ — **decided: dropped** from self-serve (see §4A,
   "Targeting").
5. **Who reviews ads**, and what review SLA is promised?
6. Will the **Android/iOS apps** get an update in Phase 1, or only via the
   unchanged proxy API until Phase 3?
7. ~~`ads.ofinit.com`~~ — **rev 2:** `www.seemacabsgoa.com/advertise`
   (or `ads.seemacabsgoa.com`), whitelisted in Seema's gateways.
