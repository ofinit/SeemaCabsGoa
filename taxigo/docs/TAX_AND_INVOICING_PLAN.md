# Fare Markup, GST & Invoicing — Plan (v2)

Status: **implemented** (pricing v2). Have the GST points marked **[CA]**
confirmed by your CA before switching GST on. Setup steps: DEPLOYMENT.md §11.

## Decisions taken (v2)

| # | Decision |
|---|---|
| D1 | The 20% is an **internal price markup**, adjustable up or down by admin. It is **never shown to customers** as a separate line or as "tax". |
| D2 | Customers pay an **all-inclusive fare + GST**. |
| D3 | The app is **Seema Holidays'** brand. Seema Holidays is the **supplier of rides and packages** to customers. **OfinIT Solutions Pvt. Ltd.** provides the platform for a **platform fee per booking**. |
| D4 | Invoices: **OfinIT → Seema Holidays** (platform fee, B2B) and **Seema Holidays → customers** (rides and packages, B2C). Seema Holidays does **not** invoice fleet operators. |
| D5 | Business and tax details (names, GSTINs, addresses, rates) are **editable in admin**. |
| D6 | GST applies **only to bookings made after GST is switched on**. Past bookings are never recalculated. |
| D7 | OfinIT's platform fee is a **percentage, changeable any time** by admin. |
| D8 | No-shows are marked **only by an admin**; no automatic no-show. |
| D9 | Customers can enter an **optional business GSTIN** at checkout and get a B2B invoice. |
| D10 | GSTINs and legal details for both companies are **entered in the admin panel** (nothing hard-coded). |

---

## 0. Urgent: fix before anything else (live security issues)

| # | Issue | Fix |
|---|---|---|
| U1 | `GET /api/update-base-fare-tab-3` needs no login, and **each call raises every in-city fare by 30%** (`routes/api.php:49`) | Delete the route and method; check tab-3 fares in `cab_price_types` against expected values |
| U2 | `GET /api/settings` (public) returns **`google_client_secret`**; confirmed live | Remove it from the response; **rotate the secret** in Google Cloud Console |
| U3 | The booking API stores fare, tax and payment amounts **sent by the client**, so a modified request can underpay | Server-side fare engine (§4.1) |

U1 and U2 should ship immediately, independent of this plan.

---

## 1. Current money flow (as built)

For a ride with fare **F** (base + airport % + surge): customer total =
**F + 20% "tax"** (`gsttitle` = 20, shown as "Tax" / "State Taxes & GST").
Online advance = `totalcommission` 20% (split `aggregatorcommission` 10% /
`operatorcommission` 10%); the rest is cash to the driver. Packages use
`packageaggregatorcommission` / `packageoperatorcommission`. `company_details`
holds test data; the accounting/report pages are static mock-ups (another
brand, `goataxi.cab`). The TDS mock-up contains GSTIN `30AAECO0806H1Z1` (Goa).

---

## 2. Turn the 20% into an internal markup (D1)

**Admin → Settings → Pricing → "Fare markup (%)"**
- Range **−50% to +100%** (negative = discount), default **20** (the
  current value, so prices don't change on rollout).
- Optional **per trip type** overrides (airport / local / sightseeing), and
  an **effective-from** date and time so a change can be scheduled (e.g.
  season pricing).
- Audit log: who changed it, from what, to what, and when.

**How it applies:** the markup is folded **into the fare** before the
customer sees it:

```
fare_shown = round( (base + airport % + surge) × (1 + markup%) )
```

Customer-facing breakdown (new bookings):

| Line | Shown to customer |
|---|---|
| Fare (all-inclusive: tolls, parking, driver allowance as today) | ✔ |
| GST @ 5% (CGST 2.5% + SGST 2.5%) | ✔, only once GST is on (§3) |
| **Total** | ✔ |
| Markup % / markup amount | ✖ admin and reports only |

**Remove "tax" wording from customer surfaces:**

| Where | Today | New |
|---|---|---|
| PWA review `customer/book/review.blade.php:62` | "State Taxes & GST included" | "All-inclusive fare" (+ GST line when on) |
| Booking email `mail/Api/order-place.blade.php:356` | "Tax & Other Charges: ₹x" | Removed; markup is inside the fare. GST line when on |
| Settings API `additional_charges` | `'Tax'` | `'GST'` only when on, else removed |
| Quote/booking API field `tax_amount` | the 20% markup | **the real GST amount** (0 until GST is on). The markup moves into `price`. Existing mobile apps then show a correct "Tax" line with no app release |

**Admin-only surfaces** (keep, renamed "Markup"): booking modal
(`view-booking-detail-modal.blade.php:310`), dashboard
(`dashboard.blade.php:95`), financial summary (`FinancialSummaryService`),
reports. Admin menu "Tax" (`menu-list.blade.php:197`, `settings/taxes.blade.php`)
is split into **Pricing → Fare markup** and **Tax → GST** (§3).

**Data:** add `markup_percent` and `markup_amount` columns to `booking_details`
(snapshot per booking). For old bookings, back-fill `markup_amount` from the
existing `tax_amount` so history reads correctly. `tax_amount` then means
real GST only from the GST start date onward (D6).

**Price impact when GST starts:** today's total is 1.20 × F. With markup 20%
+ GST 5% it becomes 1.26 × F (+5%). To keep **customer totals unchanged**, set
the markup to **14.29%** (1.20 ÷ 1.05). That's an admin choice at go-live.

---

## 3. GST on rides & packages (D2, D5, D6)

**Admin → Settings → Tax → GST** (2FA-protected):

| Field | Default | Notes |
|---|---|---|
| GST enabled | Off | Master switch |
| **GST starts from** (date-time) | — | Only bookings **created after** this get GST (D6) |
| Rate: rides (%) | 5 | Passenger transport by motor cab, without ITC **[CA]** |
| Rate: sightseeing packages (%) | 5 | Tour-operator service **[CA]** |
| SAC: rides / packages | 9964xx / 9985xx | **[CA]** exact 6-digit codes |
| Mode | Exclusive (added on top) | Matches D2 "all-inclusive + GST" |

Rates are kept with **history** (rate + effective-from); every booking
**snapshots** rate, SAC, taxable value, CGST, SGST and IGST at booking time.
Changing settings never alters existing bookings.

Goa supplier, Goa ride → **CGST + SGST** (½ each). The IGST path exists only
for an inter-state supplier or place of supply.

**Business details: Admin → Settings → Business Profiles** (D5): replaces the
test `company_details` row.

| Profile | Fields |
|---|---|
| **Seema Holidays** (supplier to customers) | Legal name, trade name, GSTIN (format + checksum validated), PAN, address, state code (30), email, phone, logo, authorised signatory + signature image, bank details, **invoice prefixes** |
| **OfinIT Solutions Pvt. Ltd.** (platform) | Same fields, plus **platform fee** settings (§5) |

Every edit is audit-logged. Issued invoices keep the details as they were at
issue time (they are snapshotted), so later edits don't change old PDFs.

**[CA] Section 9(5) check:** because OfinIT runs the technology, confirm that
OfinIT is a **software provider to Seema Holidays** and not an e-commerce
operator through which rides are supplied. If it were, GST on rides would be
payable by OfinIT instead. With a Seema Holidays-branded app and Seema
Holidays as the contracting party (terms of service, invoices), it is normally
Seema Holidays' liability.

---

## 4. Code fixes

### 4.1 Server-side fare engine (fixes U3)
- `App\Services\FareCalculator`: the **single** source for base, airport %,
  surge, **markup**, GST (CGST/SGST), advance (online) and balance (cash),
  OfinIT platform fee. All money in **integer paise**.
- Quote (`GetCabListResource`, PWA results) is stored as `fare_quotes` (inputs +
  breakdown, 15-minute expiry) and returned as `quote_id`.
- Booking (`Api\BookingController@store`, PWA `Customer\BookingController`)
  takes `quote_id`; client-sent amounts are ignored. Payment order amounts
  (Razorpay/Cashfree) come **from the stored booking**.
- Older app builds without `quote_id`: recompute server-side; if the
  client's amounts differ by more than ₹1, return "Prices updated, please
  refresh".

### 4.2 Bugs
| Location | Bug | Fix |
|---|---|---|
| `Api/BookingController.php:93` | Operator commission **100× too high** (`… : 0.00 / 100` precedence) | via `FareCalculator` |
| `Api/BookingController.php:94-98` | `number_format()` strings used in arithmetic; breaks at ≥ ₹1,000 | integer paise |
| `Api/BookingController.php:94` | Settlement GST/TDS on fare **without surge** vs customer charged **with** surge | single base |
| `Api/BookingController.php:512-514` | Sightseeing operator payment computes as **negative** | fix formula |
| Admin commission settings | `totalcommission` ≠ aggregator + operator is accepted | validate on save |

### 4.3 Tests
Unit tests for `FareCalculator` (markup ±, airport %, surge windows, GST
rounding, advance/balance split, packages); feature tests that tampered booking
requests are rejected; invoice numbering under concurrency.

---

## 5. Invoices

### 5.1 Seema Holidays → Customer (B2C): rides & packages

**Answer to "online only, or the cash balance too?":** GST is on the **whole
service value**. The online amount and the cash balance paid to the driver
are two payments for **one** supply. Seema Holidays owes GST on the full value,
including the part the driver collects in cash. So the documents follow the
payment and the ride:

| Moment | Document | Content |
|---|---|---|
| **Online advance paid** at booking | **Receipt voucher** (GST on advance) | Advance amount, GST included in it, booking ID. GST on the advance falls due in the month it is received **[CA]** |
| **Ride / package completed** | **Tax invoice** (one, full value) | Full fare + GST, then "Less: advance received ₹x (receipt voucher no.)", "Balance collected by driver ₹y". Emailed and shown in PWA "Rides" |
| **Cancelled with refund** (inside free-cancellation window) | **Refund voucher** | Reverses the receipt voucher; GST on the advance is adjusted |
| **Cancelled / no-show, advance forfeited** (not refundable under the terms) | **Tax invoice for the retained amount** | Cancellation/no-show charge = the forfeited advance (GST-inclusive). Cancellation charges are taxable like the main service (CBIC Circular 178/10/2022) **[CA]** |
| **Partial refund** | Refund voucher for the refunded part + tax invoice for the retained part | |
| Fare changes after the ride (extra km, waiting) | **Debit note**, or a final invoice that includes the extras if issued after the driver closes the trip | Driver app must record extras before invoicing |

**Triggers:** the receipt voucher fires on a verified payment webhook. The
tax invoice fires when the driver marks the trip **completed** (or admin
completes it). The refund voucher fires on refund success.

**No-show (admin only, D8):** admin → booking → **"Mark as no-show"**
(confirmation dialog, reason, audit-logged; allowed only after the pickup
time has passed and only if the trip never started). This forfeits the
advance per the cancellation terms, issues the tax invoice for the retained
amount, and notifies the customer by email/push. There's no automatic
no-show. An **"Undo no-show"** within the same day issues a credit note if an
invoice was already created.

**Cash reconciliation:** because Seema Holidays owes GST on the cash part,
the driver/fleet settlement report must show, per fleet operator, "cash
collected incl. GST ₹z", so the GST component collected in cash is accounted
for and recovered in settlement.

Series (configurable prefixes, sequential per financial year, gap-free):
`SH/26-27/RV0001` receipt vouchers · `SH/26-27/INV00001` tax invoices ·
`SH/26-27/RF0001` refund vouchers · `SH/26-27/DN0001` debit notes.

Mandatory content: Seema Holidays legal name, address, GSTIN; customer
name; invoice no. and date; place of supply (Goa, 30); SAC; taxable value;
CGST/SGST rate and amount; total in words; signature.

**Business GSTIN at checkout (D9):**
- PWA review screen (and later the apps): optional toggle **"I need a GST
  invoice for my business"**, revealing **GSTIN**, **legal business name** and
  **billing address**.
- GSTIN is validated (15-char format + checksum), with the state code read from
  it. Optional: auto-fill the legal name from a GSTIN lookup API.
- Saved on the customer profile for next time, and **snapshotted on the
  booking**; it can't be changed after the tax invoice is issued (only via
  credit note + re-issue).
- With a GSTIN, the documents are issued as **B2B** (recipient GSTIN printed,
  reported in GSTR-1 B2B so the business can claim ITC); without one, **B2C**.
- Place of supply stays Goa for rides in Goa, so CGST+SGST applies even if the
  business GSTIN belongs to another state **[CA]**.

### 5.2 OfinIT Solutions Pvt. Ltd. → Seema Holidays (B2B): platform fee

- **Platform fee setting (D7)**: a **percentage of the fare** (excluding
  GST), separate for rides and packages, **changeable any time** in admin.
  Replaces today's `aggregatorcommission` / `packageaggregatorcommission`.
  Each change is stored with a timestamp and audit log, and **every booking
  snapshots the % in force when it was booked**. A change only affects new
  bookings, so the monthly invoice stays correct even if the % changed mid-month
  (the annexure shows the % per booking).
- **Which bookings count**: completed rides and packages + admin-marked
  no-shows with a forfeited advance. Cancelled-and-refunded bookings carry no
  fee. (Assumption: change if OfinIT should earn on every paid booking.)
- **Monthly consolidated tax invoice** (OfinIT series e.g. `OFN/26-27/0001`),
  issued on the 1st for the previous month: GST **18%** (CGST 9% + SGST 9%,
  both in Goa), SAC 9983xx/9985xx **[CA]**, **annexure** listing every
  booking (ID, date, type, fare, fee).
- Bookings refunded after invoicing → **credit note** next month.
- **TDS**: if Seema Holidays deducts TDS on the platform fee (e.g. Section
  194J), record it against the invoice as "TDS deducted", not as GST **[CA]**.
- Admin → **Accounting → Platform Fee Invoices**: draft (auto-generated
  monthly) → review → **issue** (locks, numbers) → PDF → email → mark paid.
  Visible to Seema Holidays admins as **read-only + download**.

### 5.3 Engine (shared)
Tables: `business_profiles` (+ history), `tax_rates` (+ effective-from),
`booking_tax_lines` (snapshot), `invoices` (type: receipt voucher / tax invoice
/ refund voucher / credit note / debit note; party; status), `invoice_lines`,
`invoice_sequences` (row-locked counter per series per FY), `invoice_events`
(audit). PDFs via `barryvdh/laravel-dompdf` (new dependency), stored
**privately**. Exports: **GSTR-1** (B2C small, B2B, advances received,
adjustments, credit/debit notes, HSN/SAC summary) as CSV/JSON per month.
Needs a queue worker + scheduler (Coolify scheduled tasks), since today the
app runs `sync` with no scheduler. Remove the static mock-up invoice/report
pages once replaced. E-invoicing (IRN) only above ₹5 crore turnover; keep
a field for it.

---

## 6. Rollout order

| Phase | Scope | Size |
|---|---|---|
| **0 (now)** | U1 + U2 | hours |
| **1** | `FareCalculator`, quotes, server-side amounts (U3), bug fixes, tests | ~1–1.5 weeks |
| **2** | Markup setting + folding the 20% into the fare + removing "tax" wording + `tax_amount` = GST semantics + back-fill (§2). Markup stays 20%, so **customer prices don't change** | 3–4 days |
| **3** | Business Profiles + GST settings + per-booking tax snapshot (§3), GST **off** | ~1 week |
| **4** | Customer documents: receipt voucher, tax invoice, refund voucher, no-show invoice; PWA/email delivery; GSTR-1 export (§5.1) | ~2 weeks |
| **5** | OfinIT → Seema Holidays monthly platform-fee invoice + credit notes (§5.2) | ~1 week |
| **Go-live GST** | CA sign-off → set "GST starts from" → switch on → (optionally) set markup to 14.29% to hold totals | config only |

---

## 7. Open items

All business decisions are taken (D1–D10). One assumption to confirm, plus
CA items:

1. **Assumption**: the platform fee applies to completed bookings and
   admin-marked no-shows only, not to cancelled-and-refunded ones (§5.2).
2. **[CA]** Confirm: GST rates and SAC codes (rides, packages, platform fee);
   receipt vouchers on advances; GST on forfeited advances; place of supply
   for out-of-state business GSTINs; Section 9(5) position; TDS on the platform
   fee; the ~₹55.5k collected so far under a "tax" label (Section 76 CGST
   Act).
