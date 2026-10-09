# Seema Cabs Goa - Architectural Map

Welcome! This document provides a compact architectural map of the Seema Cabs Goa taxi booking platform to help you get up to speed quickly.

---

## 1. Project Purpose

[SeemaCabsGoa.com](file:///D:/Projects/India/Goa/SeemaCabsGoa.com) is a cab booking and taxi service platform tailored for Goa, India. It connects passengers, cab drivers, and fleet operators. The platform consists of two main components:
1. A static frontend site serving search-engine-optimized landing pages for airport transfers, sightseeing tours, and local rides.
2. A dynamic booking backend ("TaxiGo") that hosts an administrative dashboard and provides REST API services to native mobile applications (iOS and Android) for real-time ride orchestration, safety triggers, notifications, and invoicing.

---

## 2. Technology Stack

* **Static Frontend Site**: Vanilla HTML5, CSS3, JavaScript, and FontAwesome icons.
* **Core Application Framework**: Laravel 11.0 (PHP ^8.2 framework).
* **Asset Bundling**: Vite (^5.0.0) with [laravel-vite-plugin](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/vite.config.js) and static file copying plugins.
* **Database**: MySQL (relational database).
* **Payment Processing**: Razorpay (standard card/UPI gateway and Razorpay Route for split vendor payments).
* **Authentication**: Session-based auth for web administrative pages; Token-based API auth via Laravel Sanctum.
* **Two-Factor Authentication**: Google 2FA (TOTP-based) for admin safety.
* **Push Notifications**: Firebase Cloud Messaging (FCM) using the Kreait Firebase PHP SDK.

---

## 3. Important Directories & Modules

* [Root Directory](file:///D:/Projects/India/Goa/SeemaCabsGoa.com): Static SEO marketing landing pages (`index.html`, `airport-taxi-goa.html`, etc.), site styles (`style.css`), and assets directly at root.
* [taxigo](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo): The core Laravel 11 application directory (Admin + Customer PWA + REST APIs).
  * [app/Http/Controllers/Admin](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Admin): Administrative dashboard operations.
    * [CabManagementController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Admin/CabManagementController.php): CRUD functions for vehicle fleets.
    * [RazorpayController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Admin/RazorpayController.php): Admin payment verification and Razorpay Route transfers.
    * [TripManagementController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Admin/TripManagementController.php): Trip scheduling and booking tracking.
  * [app/Http/Controllers/Customer](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Customer): Customer PWA application controllers.
  * [app/Http/Controllers/Api](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Api): Stateless JSON endpoints consumed by the passenger and driver mobile apps.
    * [BookingController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Api/BookingController.php): Booking flows, route checks, surge calculations, and invoice setups.
    * [PaymentController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Api/PaymentController.php): Creates dynamic payments inside Razorpay API.
  * [app/Http/Middleware](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Middleware): Request filters.
    * [TwoFaSecurity.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Middleware/TwoFaSecurity.php): Enforces multi-factor safety for the administrator.
  * [app/Models](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Models): Database entities mapping tables (e.g., BookingDetail, Cab, Driver, FleetOperator, Payment, SosDetails).
  * [app/Services](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Services): External components wrapper.
    * [NotificationService.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Services/NotificationService.php): Triggers multicasts, single messages, and vendor broadcasts via Firebase FCM.
  * [app/Helper/helpers.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Helper/helpers.php): Autoloaded helper functions for image compression, lookup structures, and global state helpers.
  * [database/migrations](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/database/migrations): Schema histories.
  * [routes](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/routes): Enpoints maps ([admin.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/routes/admin.php), [customer.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/routes/customer.php), [api.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/routes/api.php), [web.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/routes/web.php)).
  * [resources/views](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/resources/views): Blade files rendering administrative panels and the customer PWA (`customer/`).

---

## 4. Frontend Architecture

The frontend is divided into two parts:
1. **Public Marketing Pages**: Handled by static, lightweight HTML pages directly under the root project directory (such as `index.html`, `airport-taxi-goa.html`). These static files load CSS stylesheets directly and link users to the Google Play Store and Apple App Store for mobile application installation.
2. **Customer PWA & Dashboard UI Pages**: Customer PWA (`resources/views/customer`) and Admin views built using Laravel Blade templates. Vite compiles the admin and customer stylesheets (`resources/scss/style.scss`, `resources/css/customer.css`) using [vite.config.js](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/vite.config.js). The production output bundles compile inside `public/build/`. Styling leverages TailwindCSS (PWA) and Bootstrap 5.2 (Admin).

---

## 5. Backend/API Architecture

The backend is built as a typical MVC application. It handles administrative dashboards alongside a stateless mobile API layer:
* **Stateless API Endpoints**: Registered in [routes/api.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/routes/api.php) and handled by controllers inside [app/Http/Controllers/Api](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Http/Controllers/Api).
* **Stateful Web Panel**: Registered in [routes/admin.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/routes/admin.php) under the `admin` prefix. Requests pass through the `web` session stack, protected by Laravel's authentication system and custom security verification layers.
* **Global Helpers**: Shared routines (e.g., custom WebP conversion, configuration lookup, string generation) reside in [helpers.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Helper/helpers.php) to prevent controller bloating.

---

## 6. Database Architecture

The MySQL database schema is structured around transaction orchestration:
* [users](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/User.php): Represents accounts, storing roles, phone numbers, type flags (e.g. `CUSTOMER` vs `DRIVER`), and social login contexts.
* [cabs](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/Cab.php) & [drivers](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/Driver.php): Represent operational vehicle and driver states. Cabs associate with color and model metadata.
* [fleet_operators](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/FleetOperator.php): Stores operator records including Razorpay Accounts for split commissions.
* [booking_details](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/BookingDetail.php): Orchestrates ride states, mapping payment logs, surge pricing details, dates, distances, statuses, sightseeing packages, and driver allocations.
* [environments](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/Environment.php): Serves as the central key-value table for runtime parameters (e.g., logos, payment keys, cancellation rules).

---

## 7. Authentication Architecture

* **Mobile App/API Level**: Authenticated via **Laravel Sanctum**. Users submit credentials, OTPs, or social auth tokens (Google/Apple) to receive a plain text login token (`createToken(Type::LoginToken)->plainTextToken`), which is passed in subsequent requests within the authorization header.
* **Admin Web Panel**: Standard session authentication. Protected routes pass through the `TwoFa` alias middleware mapping to [TwoFaSecurity.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Http/Middleware/TwoFaSecurity.php).
  * If a user has `security` set to `0` in the database, Two-Factor Authentication is bypassed.
  * If `security` is `1`, the user's `authenticate` flag must be set to `1` (indicating successful TOTP entry), otherwise, they are redirected to the admin login.

---

## 8. Important Integrations

1. **Razorpay SDK**:
   * API Payments: Orders are generated via `Razorpay\Api\Api` inside [PaymentController.php](file:///d:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Api/PaymentController.php) with amounts multiplied by 100 to parse in paisa (INR).
   * Vendor Settlements: Incorporates Razorpay Route transfers. After validating bookings, payments are split with fleet operators (`$transfer = $api->payment->fetch($id)->transfer(...)`) using custom commission math.
2. **Cashfree Payments (PG v2023-08-01 & Easy Split)**:
   * REST Client: Driven by [CashfreeService.php](file:///d:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Services/CashfreeService.php) supporting sandbox/production modes.
   * Easy Split: Supports automated vendor splits during order creation via `order_splits` array referencing the fleet operator's `cashfree_vendor_id`.
   * Webhooks: Handled by [CashfreeWebhookController.php](file:///d:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Http/Controllers/Api/CashfreeWebhookController.php) at `/api/cashfree/webhook` with HMAC SHA-256 signature verification.
   * Client-Side Checkout: Cashfree JS SDK v3 embedded in Customer PWA review & sightseeing booking flows with responsive modal checkout.
   * Admin Controls: Dynamic toggles (Enable/Disable Razorpay & Cashfree) and routing strategies (`Both`, `Razorpay Only`, `Cashfree Only`) managed via [payment-gateway.blade.php](file:///d:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/resources/views/settings/payment-gateway.blade.php).
3. **Firebase Cloud Messaging (FCM)**:
   * Driven by the [NotificationService.php](file:///d:/Projects/India/Goa/SeemaCabsGoa.com/taxigo/app/Services/NotificationService.php) instance, which loads service account configurations from the storage directory.
   * Sends multicast notifications to drivers and customers when trip updates occur or alerts are triggered.
4. **CountriesNow Space API**:
   * Uses external endpoints (`countriesnow.space/api/...`) to pull geographical list details during system initialization, populating states, cities, and countries tables.

---

## 9. Development Commands

Execute these commands inside the Laravel root directory ([taxigo](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/taxigo)):

```powershell
# Copy local configuration
Copy-Item .env.example .env

# Generate application key
php artisan key:generate

# Install backend dependencies
composer install

# Install frontend dependencies
npm install

# Run frontend assets dev server
npm run dev

# Run local Laravel server
php artisan serve

# Run migrations and seed database lookup tables
php artisan migrate --seed
```

---

## 10. Test, Lint, and Build Commands

* **Production Frontend Build**: Builds and copies assets into the target public build directories.
  ```powershell
  npm run build
  ```
* **Linting / Code Style Fixer**: Uses Laravel Pint (PHP-CS-Fixer wrappers) to fix and format codebase conventions.
  ```powershell
  ./vendor/bin/pint
  ```
* **Automated Tests**: Runs the PHPUnit suite.
  ```powershell
  php artisan test
  # OR
  ./vendor/bin/phpunit
  ```
* **Caching & Optimization**: Clears and builds configurations, routers, and views compilation logs.
  ```powershell
  php artisan optimize:clear
  ```

---

## 11. Established Coding Conventions

* **Configuration Management**: Do not hardcode configurations. Always read values from the [Environment](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Models/Environment.php) model using lookup titles (e.g. `paymentkey`, `bookingcancellationhour`).
* **Image Optimization**: Uploaded images should be converted to `.webp` files using the `UploadImageWebpConversion` helper wrapper in [helpers.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Helper/helpers.php#L32), which loads the GD driver from Intervention ImageManager.
* **Unified Responses**: API responses must extend [ResponseController.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Http/Controllers/Api/ResponseController.php) and use `$this->success()` or `$this->error()` methods to return consistent JSON shapes.
* **Enums**: Utilize defined structures inside the [App/Enums](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Enums) namespace for static types (e.g., user roles, notification types, active statuses) instead of raw strings or numbers.

---

## 12. Important Architectural & Security Constraints

* **2FA Enforcement**: When working on administrative controls or logins, the custom [TwoFaSecurity.php](file:///D:/Projects/India/Goa/SeemaCabsGoa.com/Old/code/taxigo/app/Http/Middleware/TwoFaSecurity.php) check must be applied. Do not bypass or disable it in routing tables.
* **Secrets Storage**: Firebase credentials file `firebase_credentials.json` must be stored securely at `storage/app/firebase/` and is excluded from source control. Do not place secret configurations in versioned files.
* **Razorpay Credentials**: Razorpay secret keys are loaded dynamically from the `environments` table in the database rather than from static configurations or environment variables. This enables admins to update keys on the fly.
* **Sanitation**: Before sending data payload arrays via notifications or API, use the `cleanseArray` helper to strip null elements and prevent JSON parse errors.
* **Money is server-side only**: every fare, GST, advance and platform-fee figure comes from `App\Services\Pricing\FareCalculator` (integer paise). Never store or charge amounts sent by a client; booking endpoints only compare them. Payment confirmation must go through `App\Services\Payments\PaymentVerifier` (gateway lookup), never the client's word.
* **Fare markup is internal**: `faremarkuppercent` (formerly mislabelled `gsttitle`) is folded into the fare and must never be shown to customers as a separate line or as "tax". `booking_details.tax_amount` means real GST only for `pricing_version >= 2`; v1 rows hold the old markup.
* **GST documents**: create them only through `App\Services\Invoicing\InvoiceService` (gap-free numbering, idempotent per booking). Issued invoices are immutable; corrections are credit notes. Supplier/platform legal details live in `business_profiles`, edited in admin.
* **Storage engines**: legacy tables are MyISAM (no transactions/FKs). New money tables (`invoices`, `invoice_sequences`, …) are InnoDB; don't add FKs from them to MyISAM tables.
