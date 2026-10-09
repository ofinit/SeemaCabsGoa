# Seema Cabs Goa — Self-Serve Advertising: End-to-End Plan

**Status:** Phase 0 and Phase 1 built (see §21) · **Revision 3** (consolidated;
replaces revisions 1–2) · **Owners:** Seema Holidays (seller of ad space) and
OfinIT Solutions Pvt. Ltd. (platform)

Businesses in Goa (casinos, clubs, pubs, hotels, restaurants, rentals, tours,
real estate) buy and manage ads inside the Seema Cabs Goa app (PWA + Android/
iOS), on the website and on printed in-cab cards, **by themselves**. Every ad is
**approved by an admin** before it runs. Payments go through **Seema Holidays'
payment gateway**; **Seema Holidays keeps 10%**, and **OfinIT's share
(the rest, plus GST) is split out automatically**.

Items marked **[CA]** need the chartered accountant; **[Legal]** need a lawyer.

---

## 1. Decisions taken

| # | Decision |
|---|---|
| D1 | Ad payments are collected on **Seema Holidays' own Razorpay / Cashfree** account. |
| D2 | **Seema Holidays keeps 10%** of the ad price (after discounts, before GST). **OfinIT gets the rest + GST**, split automatically. |
| D3 | **Seema Holidays is the seller** of the ad space; OfinIT supplies Seema an ad-platform and ad-operations service. |
| D4 | **No TDS** is deducted on payments to OfinIT; OfinIT issues GST invoices for every amount. |
| D5 | The gateways will be informed that Seema Holidays also sells advertising. |
| D6 | **Pricing per day**, **minimum 7 days**, with a **two-tier rate card** (Standard / Premium by advertiser category). |
| D7 | **Gender targeting is dropped.** |
| D8 | Advertisers **submit ads from the PWA**; **admin approval is mandatory**. |
| D9 | Finding-a-taxi screen has **top, bottom, large (double) and full-screen** placements. |
| D10 | Built **inside the existing Seema Laravel app** (tenant-ready, so it can later become a shared OfinIT service for other operators). |
| D11 | Every ad click carries **UTM tags** and is counted. |

---

## 2. Money flow

**Example — Standard ad sold at ₹1,000 + 18% GST**

| | Amount |
|---|---|
| Advertiser pays Seema Holidays | **₹1,180** (₹1,000 + ₹180 GST) |
| → OfinIT: 90% (₹900) + 18% GST (₹162), split automatically | **₹1,062** |
| → Seema Holidays keeps | **₹118** |
| Seema pays the government: ₹180 GST − ₹162 credit on OfinIT's invoice | ₹18 |
| **Seema Holidays' earnings** | **₹100 (10%)** |

**Example — Premium Home hero, 7 days** (₹599 × 7 = ₹4,193)

| | Amount |
|---|---|
| Advertiser pays | **₹4,947.74** (₹4,193 + ₹754.74 GST) |
| → OfinIT: ₹3,773.70 + ₹679.27 GST | **₹4,452.97** |
| → Seema keeps ₹494.77 = ₹419.30 commission + ₹75.47 net GST to remit | |

**Why Seema sells the ad (not "OfinIT sells, Seema collects"):** collecting
money in your own merchant account for another company's sale is a
payment-aggregation activity under RBI rules, breaks the gateway's
merchant-of-record terms, and needs "pure agent" GST treatment. Selling the ad
space itself and paying OfinIT through the gateway's split feature avoids all
three.

**⚠️ Input-tax credit [CA]:** the 10% / 90% split assumes Seema can offset the
GST on OfinIT's invoice (₹162 per ₹1,000) against the GST it collects. Seema's
rides use the 5% rate without input credit; the ad business is a separate 18%
supply, so credit **should** be available — the CA must confirm. If not, Seema
loses ₹62 per ₹1,000 ad, and the fallback is either OfinIT billing 90% + GST
monthly (no automatic split) while Seema keeps the full GST to remit, or
adjusting the percentages.

---

## 3. What exists today, and Phase 0 fixes

| Area | Today | Fix |
|---|---|---|
| Creation | Admin-only (`Admin\AdvertisementController`); one image for all screens; WebP conversion, no crop | PWA self-serve (§7), crop per placement (§8) |
| Approval | "Pending ads" is a static mock-up; **ads are served without checking `status`** | Serve only **paid + approved** ads (Phase 0) |
| Timing | Only dates checked; `start_time` / `end_time` ignored | Enforce date + time (Phase 0) |
| Placements | 8 screen prices; screen 3 and 8 titles swapped; API only splits "top/bottom" | Per-placement serving; fix the swap (Phase 0) |
| Prices | ₹10–₹100/day, inverted (Account ₹100, Home ₹10) | New rate card (§5) |
| Tracking | Clicks only, from the mobile apps (141 total); **PWA clicks not counted**; no UTM; clicks store user GPS | Viewable impressions, PWA clicks, UTM, no GPS (Phase 0 + §15) |
| Payments | `payment_method` text; no gateway | Gateway checkout with OfinIT split (§12) |
| Audience (Oct 2026) | Sign-ups fell from 342/month (Jan) to single digits; ~1 paid booking/month; 36 ads ever, ₹1,866 total | Measure for 30 days, then re-price (§5) |

---

## 4. Placements

Each placement has a fixed image shape; the advertiser crops once per shape.

| # | Placement | Surface | Shape · master px | Notes |
|---|---|---|---|---|
| P1 | Home hero carousel | PWA, apps | 2:1 · 1600×800 | Up to 5 rotating |
| P2 | Home inline card | PWA, apps | 3:1 · 1500×500 | Between service tiles |
| P3 | Finding-a-taxi — top banner | PWA, apps | 3:1 · 1500×500 | Above search progress; up to 3 rotating |
| P4 | Finding-a-taxi — bottom banner | PWA, apps | 3:1 · 1500×500 | Below progress; up to 3 rotating |
| P12 | Finding-a-taxi — **large card (double size)** | PWA, apps | 4:5 · 1080×1350 | Replaces the top banner area when sold; up to 2 rotating |
| P13 | Finding-a-taxi — **full-screen takeover** | PWA, apps | 9:16 · 1080×1920 | **1 advertiser per day**; once per search; close after 3 s; closes itself when a cab is found |
| P5 | Booking confirmed | PWA, apps | 2:1 · 1600×800 | Local offers near pickup / drop |
| P15 | **Airport arrival offers** | PWA, apps | 2:1 · 1600×800 | Booking confirmed **for airport pickups only** — tourists who just landed |
| P6 | Driver details / trip in progress | PWA, apps | 3:1 · 1500×500 | Never covers map, driver info or SOS |
| P7 | Ride complete / rating | PWA, apps | 1:1 · 1080×1080 | After the rating is submitted |
| P8 | Rides history & Account | PWA, apps | 3:1 · 1500×500 | Low price |
| P17 | Notifications screen | PWA, apps | 3:1 · 1500×500 | Low price |
| P14 | **Sightseeing package — sponsored stop** | PWA, apps, website | 3:1 · 1500×500 | On the package it's actually on (restaurant, spice farm) |
| P16 | **App-open sponsor** | PWA, apps | 1:1 logo + one line | "Presented by …" 1.5 s on splash; **exclusive** |
| P9 | Website landing pages | seemacabsgoa.com | 6:5 · 1200×1000 sidebar; 8:1 · 1600×200 strip | Per page group, e.g. *Goa nightclub taxi drops*, Baga / Calangute routes, airport pages |
| P10 | Booking email footer | Email | 4:1 · 1200×300 | Static image + tracked link |
| P11 | Sponsored push (later) | Apps | Text + 2:1 image | Opt-in users only; max 1/week |
| P18 | **In-cab QR card** (printed) | Cab seat-back / headrest | A6 card with QR | Tracked link; sold per cab per month; Seema prints and places |

**Finding-a-taxi rules:** at most one full-screen takeover per search;
banners hidden while it is open; nothing ever covers "Cab found" or the
booking details.

**No-ad zones (enforced in code):** login / OTP, cab search results (could be
mistaken for real cab options), payment and checkout, live trip map, SOS and
safety screens, tax invoices and receipts, error pages, the driver app.

---

## 5. Rate card

### 5.1 Tiers by advertiser category

| Tier | Categories |
|---|---|
| **Standard** | Cafés, restaurants, scooter / car rentals, shops, tours & activities, spas, local services, events (non-nightlife) |
| **Premium** | **Casinos, nightclubs, pubs & bars, hotels & resorts, real estate**, branded / national advertisers |

The advertiser declares the category; the admin verifies it at review
(§11) and can re-tier (the advertiser tops up or gets a refund).

### 5.2 Prices (per day, before 18% GST; **minimum 7 days**)

| # | Placement | Standard / day | Standard 7 days | Premium / day | Premium 7 days | Premium 30 days (−20%) |
|---|---|---|---|---|---|---|
| P13 | Finding-a-taxi — full-screen takeover (exclusive) | ₹499 | ₹3,493 | **₹1,499** | ₹10,493 | ₹35,976 |
| P16 | App-open sponsor (exclusive) | ₹399 | ₹2,793 | **₹999** | ₹6,993 | ₹23,976 |
| P15 | Airport arrival offers | ₹249 | ₹1,743 | **₹749** | ₹5,243 | ₹17,976 |
| P1 | Home hero carousel | ₹199 | ₹1,393 | **₹599** | ₹4,193 | ₹14,376 |
| P12 | Finding-a-taxi — large card | ₹199 | ₹1,393 | **₹599** | ₹4,193 | ₹14,376 |
| P9 | Website landing pages (per page group) | ₹149 | ₹1,043 | **₹499** | ₹3,493 | ₹11,976 |
| P3 | Finding-a-taxi — top banner | ₹99 | ₹693 | **₹299** | ₹2,093 | ₹7,176 |
| P5 | Booking confirmed | ₹99 | ₹693 | **₹299** | ₹2,093 | ₹7,176 |
| P14 | Sightseeing package — sponsored stop | ₹99 | ₹693 | **₹299** | ₹2,093 | ₹7,176 |
| P4 | Finding-a-taxi — bottom banner | ₹79 | ₹553 | **₹249** | ₹1,743 | ₹5,976 |
| P7 | Ride complete / rating | ₹79 | ₹553 | **₹199** | ₹1,393 | ₹4,776 |
| P2 / P6 | Home inline · Driver details | ₹49 | ₹343 | **₹149** | ₹1,043 | ₹3,576 |
| P8 / P17 | Rides history & Account · Notifications | ₹29 | ₹203 | **₹99** | ₹693 | ₹2,376 |
| — | **Finding-a-taxi bundle** (P12 + P4) | ₹249 (vs ₹278) | ₹1,743 | **₹749** (vs ₹848) | ₹5,243 | ₹17,976 |
| — | **Booking-journey bundle** (P1 + P12 + P5) | ₹449 (vs ₹497) | ₹3,143 | **₹1,299** (vs ₹1,497) | ₹9,093 | ₹31,176 |
| P18 | **In-cab QR card** | ₹499 per cab / month | — | **₹1,499** per cab / month | — | Fleet-wide packages on request |

### 5.3 Rules
- **Minimum booking 7 consecutive days** per placement; exclusive placements
  (P13, P16) are sold in 7-day blocks. Renewals and extensions are ≥ 7 days.
- **Discounts:** 7 days at list price · **14+ days −10%** · **30+ days −20%**.
- **Peak season × 1.5–2** (admin-defined windows: 15 Dec – 5 Jan, New Year
  week, Carnival, Shigmo, long weekends).
- **Category exclusivity add-on +50%** (e.g. "only casino on the full-screen
  this week").
- **Launch offer:** 50% off each advertiser's first booking for the first 2–3
  months after launch (admin switch), until the dashboard shows real numbers.
- **Coupons** (admin-created, % or ₹ off, with limits and expiry).
- Seema Holidays' **10% is on the price after discounts, before GST**.
- Every order **snapshots** prices, tier, discounts, GST and commission %.
- **Re-price after 30 days** of measured viewable impressions; check
  website traffic (Google Search Console) before selling P9.

---

## 6. Targeting
- **Area:** North Goa / South Goa / town (pickup or drop of the booking).
- **Trip type:** airport pickup (P15 is airport-only), airport drop, local,
  sightseeing.
- **Platform:** PWA, Android, iOS, website.
- **Days of week and hours** (e.g. clubs: Thu–Sun, 6 pm – 2 am).
- **Dropped:** gender (never used — 0 of 36 ads; halves a small audience;
  unreliable data; privacy and fairness risk).

---

## 7. Advertiser journey (PWA)

**Entry:** PWA **Advertise** page (`/app/advertise`) → **Create an ad**
(WhatsApp contact remains as "Need help?"). Any logged-in customer can
advertise.

1. **Business profile (first time):** business name, category (sets the
   tier), contact person, phone (OTP-verified), email, optional GSTIN + legal
   name + billing address (B2B invoice), and **licence upload for regulated
   categories** (§9). Saved for future ads.
2. **Placements:** cards with a real screenshot of where the ad appears, shape,
   Standard/Premium price for their tier, and availability.
3. **Dates:** calendar, sold-out days greyed out, **minimum 7 days**.
4. **Targeting:** area, trip type, platform, days/hours (§6).
5. **Creative:** upload → crop to each placement's shape (§8) → preview inside
   the real screen frame → headline/CTA where supported → link type
   (website / WhatsApp / call / directions).
6. **Self-check:** the advertiser checklist (§10) — must tick every item.
7. **Review & pay:** itemised price, discounts, coupon, **GST 18%**
   (CGST + SGST or IGST, §13), total → checkout on Seema Holidays' gateway;
   slots are **held for 15 minutes** while paying.
8. **In review:** confirmation push + email; review within **24 hours**.
9. **Decision:** Approved → scheduled / live · Changes requested → edit &
   resubmit (no new payment) · Rejected → automatic full refund.

**My Ads:** status, live dates, impressions, reach, clicks, CTR, calls /
WhatsApp taps, invoices, **Renew / Extend** (≥ 7 days), **Edit & resubmit**,
download report.

**Statuses:** `draft → pending_payment → in_review → changes_requested →
approved → scheduled → live ⇄ paused → expired`, plus `rejected → refunded`
and `cancelled`.

---

## 8. Creative pipeline (crop → compress → WebP)

**Browser:** Cropper.js locked to the placement's ratio (zoom, pan, rotate);
rejects images below the minimum size; uploads the **original + crop box**, so
the server crops consistently and the advertiser can re-crop without
re-uploading.

**Server (queued job):**
1. Validate the real file type (JPEG / PNG / WebP; no SVG or animated GIF),
   max 10 MB and 8000×8000, decompression-bomb guard.
2. Auto-rotate, then **strip all metadata** (EXIF / GPS).
3. Apply the crop; resize to master + 1×/2× variants + 480 px thumbnail.
4. Encode **WebP** (quality 80; step down to fit ≤ 150 KB master /
   ≤ 60 KB mobile; floor 65).
5. Store with content-hashed names and long-lived caching; **originals
   private**.

Reuses the existing `UploadImageWebpConversion` setup (GD with WebP).

---

## 9. Content policy

Follows the **ASCI code** and Indian advertising law **[Legal]**.

### 9.1 Category rules

| Category | Allowed | Not allowed | Required |
|---|---|---|---|
| **Casinos** (licensed in Goa) | Venue as **entertainment**: shows, dining, events, ambience, location | Betting, odds, "win money", jackpots, bonus credits, chips images implying winnings, online / real-money gaming | **21+ notice**, "Play responsibly", valid Goa casino licence on file; **two-admin approval** **[Legal]** |
| **Nightclubs, pubs & bars** | Venue, music, DJs, events, food, ambience, entry details | **Alcohol brands, drink images, liquor prices or offers** ("happy hour", "free pint") — direct or surrogate liquor ads | Age notice per the venue's licence; trade / excise licence on file |
| **Hotels & resorts** | Rooms, offers, dining, location | Fake ratings / "No. 1" claims without proof | Registration / GSTIN on file |
| **Real estate** | Projects, plots, villas | Guaranteed-return claims | **RERA number visible** in the creative |
| **Restaurants & cafés** | Food, menus, offers | Liquor offers (as above) | FSSAI licence on file |
| **Rentals & tours** | Vehicles, tours, prices | Unlicensed operators | Relevant permits on file |

### 9.2 Prohibited for everyone
Alcohol and tobacco / vape products; betting, gambling, lottery and
real-money games; adult or sexual content; drugs; weapons; political or
religious content; hate or discrimination; fake urgency or misleading
claims; unverified health, weight-loss or financial claims; impersonation of
Seema Cabs, government or other brands; **competing taxi / cab services**;
content that violates copyright or uses people's photos without consent.

### 9.3 Always shown
A **"Sponsored"** label (added by the system), and a **Report ad** option.

---

## 10. Advertiser checklist (before submitting)

Shown as tick boxes in the PWA; every box must be ticked. Automated checks run
at the same time.

**Business**
- [ ] The business name and category are correct (the category sets the price tier).
- [ ] I've uploaded the licence required for my category (casino, bar / pub, FSSAI, RERA…), valid for the whole ad period.
- [ ] The phone, WhatsApp and website in the ad belong to my business.
- [ ] (Optional) My GSTIN, legal name and billing address are correct for the GST invoice.

**Creative**
- [ ] I checked the preview on every placement; the text is readable on a small phone.
- [ ] Prices in the ad include all taxes, and any offer shows its validity dates.
- [ ] I own or have rights to every image, logo and photo, and people shown have consented.
- [ ] No alcohol brands, drink images or liquor offers.
- [ ] No betting, gambling, "win money" or jackpot claims.
- [ ] Casinos: the ad shows **21+** and "Play responsibly". Real estate: the **RERA number** is visible.
- [ ] No adult, offensive, political or misleading content, and no claims I can't prove.

**Link**
- [ ] The link opens on a phone over HTTPS, matches the ad, and doesn't auto-download anything.

**Agreement**
- [ ] I accept the Advertising Terms, Content Policy and Refund Policy, and understand the ad runs only after approval.

**Automated checks (block or warn):** image type, size and dimensions;
NSFW / nudity detection; keyword filter in the headline and in-image text
(liquor and betting words: beer, whisky, vodka, happy hour, bet, odds, win,
jackpot…); link reachable, HTTPS, not a URL shortener, not on Google Safe
Browsing's list; GSTIN checksum; licence file attached for regulated
categories; duplicate-creative detection.

---

## 11. Admin approval checklist

**Admin → Advertisements → Pending approval.** Each item is marked Pass or
Fail with a reason code; one Fail blocks approval. Target **24 hours**.

**A. Advertiser & payment**
- [ ] Payment received and verified with the gateway (system check).
- [ ] Business identity is real; GSTIN valid and matches the legal name (if given).
- [ ] Category and tier are correct (re-tier if not → advertiser tops up or is refunded).
- [ ] Required licence uploaded, legible, in the advertiser's name, valid through the end date.
- [ ] Advertiser isn't blocked and has no unresolved reports.

**B. Creative**
- [ ] Renders correctly in every booked placement on a small phone (crop, legibility, nothing cut off).
- [ ] Nothing from the prohibited list (§9.2), including surrogate liquor or gambling imagery.
- [ ] Category rules met (§9.1): casino 21+ + "Play responsibly"; RERA number; no liquor offers for pubs / clubs.
- [ ] Claims are truthful and provable; offers show validity; prices include taxes.
- [ ] No competing taxi / cab service; no impersonation; no third-party brands without rights.
- [ ] Not offensive for a family-friendly cab app.

**C. Link**
- [ ] Opens over HTTPS, loads on mobile, matches the ad, no unexpected redirects or downloads.
- [ ] Phone / WhatsApp numbers belong to the advertiser.

**D. Schedule**
- [ ] Dates, slots and exclusivity have no conflicts; peak pricing and discounts were applied correctly.

**E. Decision**
- **Approve** → scheduled / live; advertiser notified.
- **Request changes** (reason codes) → advertiser edits and resubmits, no new payment.
- **Reject** (reason codes) → **automatic full refund**, OfinIT's split reversed, credit notes, notification.
- **Casinos and anything flagged by the automated checks need a second admin's approval.**
- If approval comes after the start date, the end date moves forward by the delay.
- Every check and decision is audit-logged with the admin's name and time.

**Reason codes:** R01 image quality · R02 wrong crop / unreadable · R03 alcohol
content · R04 gambling / betting claim · R05 misleading or unproven claim ·
R06 link broken / unsafe · R07 licence missing / expired · R08 category / tier
mismatch · R09 adult / offensive · R10 competitor or impersonation · R11
image or brand rights · R12 other (free text).

**After approval (automatic):** daily link check (broken → pause + notify);
**licence expiry tracking** (auto-pause on expiry); **3 user reports →
auto-pause** pending re-review; admins can pause any live ad.

---

## 12. Payments, splits & refunds

- **Gateway:** Seema Holidays' Razorpay / Cashfree (same as rides). Checkout
  on **`www.seemacabsgoa.com/advertise`** (must match the gateway's whitelisted
  domain).
- **Same safeguards as rides:** order amount from the stored campaign order,
  confirmation verified with the gateway (`PaymentVerifier`), signed webhooks,
  idempotency, nightly reconciliation.
- **Split to OfinIT** (reuses `PlatformFeeTransferService`): Razorpay Route
  transfer to OfinIT's linked account (`acc_Qm5h0HughTNOA3`), or Cashfree
  `order_splits` to OfinIT's vendor id. Amount = 90% of the net price + GST.
- **Refunds:** rejection → full refund automatically; cancellation before
  start → full refund; after start → pro-rata for unused days (admin
  decision). Razorpay `reverse_all` / Cashfree `refund_splits` return
  OfinIT's share; credit notes on both sides.
- **Offline sales** (cash / bank transfer, admin-entered): no split; OfinIT's
  share is listed as **payable** on its monthly invoice.
- **No TDS** (D4).

---

## 13. GST & invoices

**At checkout:** prices shown before GST; **18% added**. Which GST [CA]:

| Advertiser | GST |
|---|---|
| Goa business, or individual with a Goa address | CGST 9% + SGST 9% |
| Business registered in another state | IGST 18% |
| Individual without GSTIN | by address on record, else Goa |

**Invoices** (reuses `InvoiceService` + business profiles):
- **Seema Holidays → advertiser:** tax invoice at payment, new series (e.g.
  `SH/26-27/AD00001`); B2B with the advertiser's GSTIN for input credit.
  Credit notes for refunds.
- **OfinIT → Seema Holidays:** monthly invoice line "Ad platform &
  operations" (own SAC) — 90% of net ad revenue + 18% GST, marked collected
  (split) or payable (offline).
- **Returns:** GSTR-1 export extended with ad invoices (B2B, B2B inter-state,
  B2C, credit notes); Seema pays in GSTR-3B.
- **Settings → GST:** ad GST rate (18%) and ad SAC codes **[CA]**.

---

## 14. Slots, scheduling, expiry & renewal

- **Admin → Advertisements → Placements:** name, shape, Standard and Premium
  price per day, **slots** (ads in rotation), platforms, active on/off,
  sample screenshot.
- **Availability:** a day sells out when approved + in-review + held ads
  reach the slots; exclusive placements have 1 slot. Admins see a booking
  calendar per placement.
- **Serving:** only **paid + approved** ads, inside their date **and time**
  window and targeting. Rotation shares impressions equally; a house ad
  ("Advertise here") fills empty slots. Frequency cap: the same ad at most
  3 times per user per placement per day.
- **Expiry:** an hourly job marks ended ads Expired and removes them.
- **Reminder 3 days before expiry** with one-tap **Renew** (≥ 7 days, same
  creative, no re-approval unless the creative or link changes).
- **Final report** emailed at expiry.

---

## 15. Tracking, analytics & privacy

**Events:** served; **viewable impression** (≥ 50% visible for ≥ 1 s);
click; call / WhatsApp / directions tap; QR scan (P18).

**Click tracking & UTM:** every ad links to
`www.seemacabsgoa.com/ads/c/{signed-token}` → click logged → 302 to the
stored landing URL with:

```
utm_source=seemacabsgoa
utm_medium=pwa | android | ios | website | qr
utm_campaign=<ad order id>
utm_content=<placement>
```

The advertiser's own UTM tags are kept; only missing ones are added. Only the
stored landing URL is allowed (no open redirect). WhatsApp ads open with a
prefilled "Hi, I saw your ad on Seema Cabs Goa".

**Invalid traffic:** bots and repeated clicks (1 per ad per device per 30
min) are filtered and never billed or shown.

**Advertiser dashboard:** impressions, reach, frequency, clicks, CTR, calls /
WhatsApp taps, QR scans, by day, hour, placement, area and platform; CSV / PDF
export; weekly email; final report.

**Privacy (DPDP):** no breakdown covering fewer than 10 users; advertisers
never see personal data; no GPS stored with clicks; consent for marketing
push.

**Admin dashboard:** revenue (gross, GST, Seema 10%, OfinIT share), fill rate
per placement, pending reviews and SLA, refunds, top advertisers, reports.

---

## 16. Admin screens

| Screen | Purpose |
|---|---|
| Advertisements → **Pending approval** | Review queue with the checklist (§11) |
| Advertisements → **Live / Scheduled / Paused / Expired** | Manage, pause, extend, renew (existing lists extended) |
| Advertisements → **Placements & prices** | Shapes, slots, Standard / Premium prices (replaces Screen Price) |
| Advertisements → **Pricing rules** | Peak windows, discounts, exclusivity add-on, launch offer, coupons |
| Advertisements → **Advertisers** | Profiles, categories / tiers, licences and expiry, block / unblock |
| Advertisements → **Reports** | User reports and auto-paused ads |
| Settings → **Ad commission** | Seema Holidays % (default 10), audited |
| Invoices | Ad invoices, credit notes, GSTR-1 export (existing page extended) |
| Reports → OfinIT Fee Transfers | Split status (existing page, extended to ads) |

---

## 17. Data model (inside the Seema app, `ad_` prefix)

`ad_placements` (code, shape, sizes, slots, standard / premium price, active)
· `ad_categories` (name, tier, required licence type) · `ad_advertisers`
(user, business, category, GSTIN, legal name, address, status) ·
`ad_licences` (type, number, file (private), valid until, verified by) ·
`ad_campaigns` (advertiser, status, dates, times, targeting, price snapshot,
commission %, GST snapshot) · `ad_campaign_placements` (placement, days,
price / day, subtotal) · `ad_creatives` (original (private), crop box,
variants, status) · `ad_orders` / payment rows (gateway, transfer tracking —
same pattern as ride payments) · `ad_reviews` (checklist JSON, decision,
reason codes, reviewer, second reviewer) · `ad_reports` · `ad_events` ·
`ad_stats_daily` · `ad_coupons` · `ad_pricing_rules`. Invoices reuse the
`invoices` tables with new document types.

The existing `advertisements`, `advertisers`, `screen_prices` and
`advertisement_user_clicks` data is migrated; the existing mobile-app API
(`/api/advertisement-list`, `/api/advertisement-click`) keeps its response
shape so current app versions keep working.

---

## 18. Security & operations
- Advertiser login uses the existing customer account (OTP / Google); admin
  approval actions require the admin 2FA login.
- Licences and original images are **private** files (not in public storage).
- Signed click / impression tokens; rate limits; open-redirect protection.
- Scheduled jobs (expiry, link checks, licence expiry, reminders, stats
  roll-ups) run on the Coolify scheduler (`php artisan schedule:run`).
- Alerts for failed payments, failed splits, review SLA breaches.

---

## 19. Delivery phases

| Phase | Scope | Size |
|---|---|---|
| **0 — Quick fixes** | Serve only paid + approved ads; enforce times; per-placement serving; fix screen 3 / 8; "Sponsored" label; PWA click counting + UTM; viewable impressions; stop storing click GPS | ~1 week |
| **1 — Self-serve MVP** | Advertiser profile + licences; PWA wizard (P1–P8, P12, P13); crop → WebP; two-tier pricing, 7-day minimum, discounts; checkout with OfinIT split + refunds; ad GST invoices; admin approval with checklist, reason codes, two-person rule; placements & slots admin; expiry, reminders, renewal; basic dashboard; migrate existing ads | ~4–5 weeks |
| **2 — Growth** | P14 package stop, P15 airport arrivals, P16 app-open sponsor, P9 website, P10 email footer; peak pricing, exclusivity add-on, launch offer, coupons; reports & auto-pause; licence-expiry automation; weekly reports; tracked click redirect for all surfaces | ~3–4 weeks |
| **3 — Scale** | P18 in-cab QR programme; P11 sponsored push; native app placements (app release); conversion tracking; agency accounts; other TaxiGo operators | ongoing |

---

## 20. Open items

1. **[CA]** Structure (§2), SAC codes, Seema's input credit on OfinIT's 18% invoice.
2. **[Legal]** Casino and pub / club advertising rules (§9.1), the Advertising Terms and Content Policy text.
3. Confirm the **rate card** (§5.2) and launch offer.
4. **Who reviews** ads (names for first and second approval) and the 24-hour SLA.
5. **Android / iOS apps:** update in Phase 1, or rely on the unchanged API until Phase 3?
6. **Fleet size** for the in-cab QR programme (sets P18 packages).

---

## 21. Implementation status

**Phase 0 (built):** only paid + approved ads inside their date and time
window are served; per-screen slots; screen 3 / 8 fixed; "Sponsored" label;
signed click redirect with UTM; viewable impressions; no click GPS.

**Phase 1 (built):**

| Area | Where |
|---|---|
| Tables (`ad_placements`, `ad_categories`, `ad_advertisers`, `ad_licences`, `ad_campaigns`, `ad_campaign_placements`, `ad_creatives`, `ad_reviews`) | migration `2026_10_10_000001` |
| Placements P1–P8, P12, P13 with the §5.2 rate card; categories with tiers, licence and two-admin flags | seeded by the migration; edited in Admin → Advertisements → Placements & Pricing |
| PWA: business profile, licence upload, wizard (placements → dates with sold-out days → crop per shape → link → checklist → pay), My ads with views / taps / CTR, invoices, renew | `/app/advertise`, `Customer\AdvertiseController` |
| Crop → WebP: Cropper.js in the browser; server validates type / size, keeps the original privately, strips metadata, crops, resizes to master, WebP ≤ 150 KB | `AdCreativeProcessor` |
| Pricing: per day × tier, 7-day minimum, 14+ −10%, 30+ −20%, GST 18% (CGST + SGST, IGST for other-state GSTINs), Seema 10% / OfinIT 90% + GST | `AdPricing`, `AdSettings` |
| Checkout on Seema's Razorpay / Cashfree; gateway verification; OfinIT split (Route transfer / Easy Split); 15-minute slot hold; webhook + hourly reconciliation | `AdPaymentService`, `CashfreeWebhookController`, `ads:maintain` |
| Review queue with the §11 checklist, reason codes, two-admin rule (category or automated flags), approve / request changes / reject with automatic refund, pause, cancel with pro-rata refund | Admin → Advertisements → Ad Review Queue, `AdCampaignService` |
| GST documents: Seema → advertiser tax invoice (`…/AD…`), credit notes on refunds, monthly OfinIT → Seema ad-platform invoice (`…/AP…`) | `InvoiceService` |
| Serving: approval creates one `advertisements` row per placement, so the Phase 0 server, mobile API and tracking are reused. New PWA slots: home inline (P2), booking confirmed (P5), driver details (P6), ride complete (P7), rides & account (P8), finding-a-taxi large card (P12) and full screen (P13) | `AdServer`, `customer.components.ad-slot` |
| Expiry, 3-day renewal reminder, renewals with the same image and link go live without re-review | `ads:maintain` (hourly) |

**Deferred to Phase 2:** targeting (area, trip type, platform, days / hours),
bundles, peak pricing, exclusivity add-on, launch offer, coupons; phone OTP
for the advertiser profile (the logged-in customer account is used); NSFW
detection and Google Safe Browsing (keyword, shortener, duplicate-image and
link checks are in); 1× / 2× image variants; frequency cap; Report-ad button
and auto-pause on reports; licence-expiry auto-pause; weekly / final report
emails and CSV / PDF export; GSTR-1 split of ad invoices by section beyond
the existing export; P14–P18 placements. Older Android / iOS builds keep the
unchanged API and never receive the new PWA-only shapes (P2, P12, P13).

**Operations:** add the Coolify volume `/var/www/html/storage/app/ads`
(licences and original images, private) and keep the scheduler running.
Two admin accounts are needed for casino ads and flagged ads.

