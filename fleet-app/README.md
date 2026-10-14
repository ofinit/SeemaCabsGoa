# Seema Cabs Fleet (Android)

Android app for the fleet operator (Seema Holidays). It opens
`https://seemacabsgoa.com/fleet` (served by the Laravel app, `taxigo/routes/fleet.php`)
and receives Firebase push alerts:

- **New booking**: loud alert on its own "New bookings" channel (custom sound), as
  soon as a customer's payment is confirmed. Tapping it opens the booking.
- **Booking cancelled**: same channel.
- **Bookings list** (Upcoming, Today, Completed, Cancelled, All) and **booking details**:
  customer (call / WhatsApp), pick-up and drop-off, date and time, cab, driver, fare split,
  and trip status (Confirmed → Driver assigned → Completed).

Log in with the fleet operator account (the admin login works too). Google
Authenticator applies if it's switched on for the account.

The screens are web pages on the server. Changing them doesn't need a new APK; only
native changes (icon, sound, app name, plugins) do.

## One-time Firebase setup (needed for alerts)

1. [Firebase console](https://console.firebase.google.com) → project **seema-cabs-goa**
   → Project settings → **Add app → Android**.
2. Package name: `com.seemacabsgoa.fleet`; nickname: Seema Cabs Fleet. Register the app.
3. Download **google-services.json** and put it at `fleet-app/android/app/google-services.json`.
   It's git-ignored; don't commit it.
4. Rebuild the APK (below).

## Build the APK (Windows)

Needs Node, JDK 21 and the Android SDK (platform 36, build-tools 36).

```
cd fleet-app
npm install
npx cap sync android
cd android
gradlew.bat assembleRelease
```

The signed APK is written to `android/app/build/outputs/apk/release/app-release.apk`.

## Signing key: keep it safe

`fleet-app/keystore/seema-cabs-fleet.jks` and its passwords (`keystore/keystore.properties`,
copied to `android/keystore.properties` for the build) sign every release.

- **Back up both files.** Updates must be signed with the same key, or phones have to
  uninstall the old app first.
- **Never commit them.** They are git-ignored.

## Versions

Bump `versionCode` (by 1) and `versionName` in `android/app/build.gradle` for each new APK.
