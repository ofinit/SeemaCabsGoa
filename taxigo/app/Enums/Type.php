<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Type extends Enum
{
    //login type
    const GOOGLE = 2;
    const APPLE = 3;

    const ADMIN = 1;
    const FleetOperator = 2;

    const CUSTOMER = 3;

    const DRIVERASSIGNSTATUS = 1;

    const ActiveUser = 1;

    const LoginToken = 'GoaTaxiLoginToken';

    const DeleteAccountKey = 'delete_account';
    const CabModelKey = 'cab_model';
    const CabTypeKey = 'cab_type';
    const AirPortTransferKey = 'air_port_transfer';
    const AppUpdateKey = 'app_update';
    const AppUpdateTitle = 'appupdate';
    const AppUpdateAvailable = 1;
    const AppVersion = 'appnewversion';
    const AppAndroidUrl = 'androidappurl';
    const AppIosUrl = 'iosappurl';
    const AppLogo = 'applogo';
    const SplashScreenLogo = 'splashscreenlogo';
    const AdminPanelLogo = 'adminpanellogo';
    const MAIL_LOGO = 'maillogo';
    const AppLogoKey = 'app_logo';
    const GstTitle = 'gsttitle';
    const TdsTitle = 'tdstitle';
    const TotalCommission = 'totalcommission';
    const FleetOperatorCommission = 'operatorcommission';
    const CompanyCommission = 'aggregatorcommission';
    const SURGE_PRICE = 'surge-pricing';
    const MIN_BOOKING_TIME = 'bookinghour';
    const BOOKING_CANCELLATION = 'bookingmaxhour';

    const BOOKING_CANCELLATION_HOUR = 'bookingcancellationhour';
    const BOOKING_CANCELLATION_MIN_HOUR = 'bookingcancellationminhour';

    const AIRPORT_PICKUP_PERCENTAGE = 'airport_pickup_percentage';

    const PackageAggregatorCommission = 'packageaggregatorcommission';
    const PackageOperatorCommission = 'packageoperatorcommission';

    const SURGE_PRICE_ENABLED = 'on';
    const AllValue = 10001;

    const ENABLED = 1;
    const DISABLED = 0;
    const PAID = 1;
    const DEFAULT = 1;
    const UNPAID = 2;
    const REFUND = 4;

    const INACTIVE = 0;
    const ACTIVE = 1;

    const CURRENCY_SIGN = '₹';
    const INDIA_ID = 99;

    const AIRPORT_PICKUP = 1;
    const AIRPORT_DROP = 2;
    const CITY_RIDES = 3;

    const PAYMENT_KEY = "paymentkey";
    const PAYMENT_SECRETE = "paymentsecrete";
    const RAZORPAY_WEBHOOK_SECRET = "razorpaywebhooksecret";
    const RAZORPAY_ENABLED = "razorpay_enabled";

    const CASHFREE_APP_ID = "cashfree_app_id";
    const CASHFREE_SECRET_KEY = "cashfree_secret_key";
    const CASHFREE_MODE = "cashfree_mode";
    const CASHFREE_WEBHOOK_SECRET = "cashfree_webhook_secret";
    const CASHFREE_ENABLED = "cashfree_enabled";

    const PRIMARY_PAYMENT_GATEWAY = "primary_payment_gateway";

    const BOOKING_CANCELLATION_STATUS = 3;

    const AIRPORT_ONE = 'Dabolim Goa Airport (GOI)';
    const AIRPORT_TWO = 'Manohar International Airport (GOX)';
    const AIRPORT_ONE_ID = 1;
    const AIRPORT_TWO_ID = 2;

    // Cab Type
    const CAB_HATCHBACK = 'Hatchback';
    const CAB_SEDAN = 'Sedan';
    const CAB_SUV = 'SUV';
    const CAB_HATCHBACK_ID = 1;
    const CAB_SEDAN_ID = 2;
    const CAB_SUV_ID = 3;

    const SWC_STATUS = 1;
    const DNC_STATUS = 0;
}
