# Ads in the Android / iOS apps — integration guide

For the developers of the Seema Cabs Goa customer apps. The server side is
live; this explains what the apps need to call and how ads must be shown.
Older app builds keep working: they use `/api/advertisement-list` and never
receive the newer ad shapes.

## 1. Screens and image sizes

Ask for ads one screen at a time. Show the image at its aspect ratio
(width × height below is the master size of the image we send).

| Screen id | Where in the app | Shape (px) | How many to show |
|---|---|---|---|
| 1 | Home — hero carousel | 2:1 (1600×800) | up to 5, rotate every ~4.5 s |
| 9 | Home — inline card between service tiles | 3:1 (1500×500) | 1 |
| 3 | Finding a taxi — top banner | 3:1 (1500×500) | 1 |
| 10 | Finding a taxi — large card (replaces the top banner when present) | 4:5 (1080×1350) | 1 |
| 8 | Finding a taxi — bottom banner | 3:1 (1500×500) | 1 |
| 11 | Finding a taxi — full-screen takeover | 9:16 (1080×1920) | 1, see §4 |
| 4 | Booking confirmed | 2:1 (1600×800) | 1 |
| 12 | Airport arrival offers — booking confirmed / trip, **airport pickups only** | 2:1 (1600×800) | 1 |
| 5 | Driver details / trip in progress | 3:1 (1500×500) | 1 |
| 6 | Ride complete | 1:1 (1080×1080) | 1 |
| 7 | Rides history and Account | 3:1 (1500×500) | 1 |
| 13 | Sightseeing package page — sponsored stop | 3:1 (1500×500) | 1 |
| 14 | App open (splash) — "Presented by" logo + one line | 1:1 logo + `headline` | 1, ~1.5 s |

**Never show ads on:** login / OTP, cab search results, payment and
checkout, the live trip map, SOS and safety screens, invoices and receipts,
error screens, or anywhere in the driver app.

## 2. Getting ads

```
GET /api/ads?screen=3&platform=android&state=<state id>
             [&area=north|south] [&trip=airport_pickup|airport_drop|local|sightseeing] [&package_id=<id>]
Authorization: Bearer <token>   (optional)
```

`area`, `trip` and `package_id` describe the current booking. Send them on
the trip screens and the package page, and leave them out elsewhere. For
screen 13, `package_id` is required.

Response (same envelope as other APIs; ads are in `data.data`):

```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 104,
        "banner_image": "https://www.seemacabsgoa.com/storage/banner/ad-…-1500x500.webp",
        "banner_url": "https://example.com/offer",
        "click_url": "https://www.seemacabsgoa.com/ads/c/104?p=android&s=3&signature=…",
        "sponsored": true,
        "screen": 3,
        "width": 1500,
        "height": 500,
        "headline": null
      }
    ],
    "second_data": []
  }
}
```

An empty `data` means there's nothing to show. Hide the slot; don't show a
placeholder.

## 3. Taps, views and reports

- **Tap:** open `click_url` in the system browser (or an in-app browser).
  It records the tap and forwards to the advertiser with tracking added.
  Never open `banner_url` directly. If `click_url` is null, the ad isn't
  tappable.
- **View:** count a view when at least 50% of the ad has been on screen for
  1 second, at most once per screen visit. Send views in batches (up to
  20) when the screen closes or every few seconds:

  ```
  POST /api/ads/impressions
  {"platform": "android", "items": [{"id": 104, "screen": 3}, {"id": 98, "screen": 1}]}
  ```
- **Sponsored label and report:** every ad shows a small "Sponsored ⓘ" tag
  in the top-right corner. Tapping the tag (not the ad) opens a sheet:
  "This ad was paid for by a local business and checked by our team. Is
  something wrong with it?" with these reasons:

  | `reason` | Text |
  |---|---|
  | `misleading` | Misleading or a scam |
  | `offensive` | Offensive or inappropriate |
  | `alcohol_gambling` | Alcohol, betting or gambling |
  | `broken` | Link doesn't work |
  | `irrelevant` | Not relevant / annoying |
  | `other` | Something else |

  ```
  POST /api/ads/report
  Authorization: Bearer <token>   (optional; one report per person per ad)
  {"id": 104, "reason": "misleading", "platform": "android"}
  ```

## 4. Special placements

- **Full screen (11):** show it once per cab search, over the "finding a taxi"
  screen, with the "Sponsored" tag. The close button activates after
  3 seconds. Close it automatically when a cab is found, and never cover
  "Cab found" or the booking details. Hide banners 3, 8 and 10 while it's
  open.
- **Large card (10):** if screen 10 returns an ad, show it in place of the
  top banner (3).
- **App open (14):** on the splash screen, show `banner_image` as a small
  square logo with "Presented by · Sponsored" and `headline` underneath.
  Don't make the splash screen any longer because of it.
- **Airport arrivals (12):** only request and show it for airport pickups
  (`trip_type = 1`).

## 5. Sponsored push (P11)

Customers opt in to "Offers from Goa businesses". It's off by default; ask
on the account or settings screen, never by default.

```
GET  /api/ads/push-opt-in            → {"status": true, "data": {"on": false}}
POST /api/ads/push-opt-in {"on": true}
Authorization: Bearer <token>   (required)
```

Sponsored pushes arrive as normal FCM notifications (title starts with
"Sponsored ·", with an image) with this data payload:

```json
{"type": "sponsored", "link": "https://www.seemacabsgoa.com/ads/c/…?p=push&s=18&signature=…", "ad_id": "131"}
```

When `type` is `sponsored`, open `link` in the browser on tap. Each customer
gets at most one sponsored push a week, between 10 am and 8 pm.

## 6. Checklist before release

- [ ] Every screen in §1 requests its own `screen` id and sends the right `platform` (`android` or `ios`).
- [ ] No ads on the screens in the "never" list.
- [ ] "Sponsored ⓘ" tag on every ad; tapping it opens the report sheet.
- [ ] Taps open `click_url`; views follow the 50% / 1 second rule.
- [ ] Full screen: closable after 3 seconds, once per search, closes when a cab is found.
- [ ] Push opt-in is off by default and can be turned off any time; sponsored pushes open `link`.
