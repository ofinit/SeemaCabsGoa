<?php

namespace App\Services;

use App\Models\Notification as ModelsNotification;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Models\VendorDeviceToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RegistrationToken;

class NotificationService
{
    protected $messaging;

    protected array $tokens = [];
    protected array $userIdsList = [];

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function setAllUserToken()
    {
        $tokens = UserFcmToken::whereNotNull('user_id')->distinct()->pluck('token')->toArray();
        $userIds = UserFcmToken::whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();
        $this->userIdsList = $userIds;
        $this->tokens = $tokens;
    }

    public function cleanseArray(array $data)
    {
        return array_filter($data, function ($value) {
            if (is_array($value)) {
                return !empty(array_filter($value, fn($v) => !is_null($v)));
            }
            return !is_null($value);
        });
    }

    public function sendNotification(array $tokens, string $title, string $body, array $data = [], $userId = null)
    {
        Log::info('Notification send started...');

        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        $totalSuccess = 0;
        $totalFailure = 0;

        foreach (array_chunk($tokens, 500) as $chunk) {
            $registrationTokens = array_map(function ($token) {
                return RegistrationToken::fromValue($token);
            }, $chunk);

            $report = $this->messaging->sendMulticast($message, $registrationTokens);

            $totalSuccess += $report->successes()->count();
            $totalFailure += $report->failures()->count();

            foreach ($report->failures()->getItems() as $failure) {
                Log::warning('Failed token: ' . $failure->target()->value());
                Log::warning('Error: ' . $failure->error()->getMessage());
            }
        }

        try {
            ModelsNotification::create([
                'user_id' => $userId,
                'title' => $title,
                'text' => $body,
                'data' => json_encode($data),
            ]);
            Log::info("Notification stored.");
        } catch (\Throwable $th) {
            Log::error('Notification not stored. ' . $th->getMessage());
        }

        Log::info("Notification sending complete. Success: $totalSuccess | Failures: $totalFailure");

        return [
            'success' => $totalSuccess,
            'failure' => $totalFailure,
        ];
    }

    public function sendUserNotification($token, string $title, string $body, array $data = [], $userId = null)
    {
        Log::info('Notification send started...');

        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        $registrationToken = RegistrationToken::fromValue($token);

        $this->messaging->sendMulticast($message, $registrationToken);

        // try {
        //     ModelsNotification::create([
        //         'user_id' => $userId,
        //         'title' => $title,
        //         'text' => $body,
        //         'data' => $data,
        //     ]);
        //     Log::info("Notification stored.");
        // } catch (\Throwable $th) {
        //     Log::error('Notification not stored. ' . $th->getMessage());
        // }

        Log::info("Notification sending complete.");

        return true;
    }

    public function storeAllUserNotification(string $title, string $text, array $data, string $link)
    {
          $users = User::pluck('id');

        foreach ($users as $userId) {
            ModelsNotification::create([
                'user_id' => $userId,
                'title'   => $title,
                'text'    => $text,
                'data'    => json_encode($data),
                'link'    => $link,
            ]);
        }
    }

    public function storeUserNotification(string $userId,string $title, string $text, array $data, string $link)
    {
            ModelsNotification::create([
                'user_id' => $userId,
                'title'   => $title,
                'text'    => $text,
                'data'    => json_encode($data),
                'link'    => $link,
            ]);
    }

    public function sendBulkNotification(string $title, string $body, array $data = [])
    {
        $tokens = $this->cleanseArray($this->tokens);
        if (empty($tokens)) {
            return;
        }

        Log::info('Notification send started...');

        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        $totalSuccess = 0;
        $totalFailure = 0;

        foreach (array_chunk($tokens, 500) as $chunk) {
            $registrationTokens = array_map(function ($token) {
                return RegistrationToken::fromValue($token);
            }, $chunk);

            $report = $this->messaging->sendMulticast($message, $registrationTokens);

            $totalSuccess += $report->successes()->count();
            $totalFailure += $report->failures()->count();

            foreach ($report->failures()->getItems() as $failure) {
                Log::warning('Failed token: ' . $failure->target()->value());
                Log::warning('Error: ' . $failure->error()->getMessage());
            }
        }

        try {
            if(!empty($this->userIdsList)) {
                foreach ($this->userIdsList as $userId) {
                    ModelsNotification::create([
                        'user_id' => $userId,
                        'title' => $title,
                        'text' => $body,
                        'data' => empty($data) ? null : $data,
                    ]);
                }
            }
            Log::info("Notification stored.");
        } catch (\Throwable $th) {
            Log::error('Notification not stored. ' . $th->getMessage());
        }

        Log::info("Notification sending complete. Success: $totalSuccess | Failures: $totalFailure");

        return [
            'success' => $totalSuccess,
            'failure' => $totalFailure,
        ];
    }

    public function sendVendorNotification(string $date)
    {
        Log::info('Vendor Notification send started...');

        $token = VendorDeviceToken::pluck('fcm_token')->toArray();
        $data = ['date' => $date];
        $notification = Notification::create("New Booking", 'Alert! New Taxi Booking Received');
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);

        $this->messaging->sendMulticast($message, $token);

        try {
            Log::info("Vendor  Notification stored.");
        } catch (\Throwable $th) {
            Log::error('Vendor  Notification not stored. ' . $th->getMessage());
        }

        Log::info("Vendor Notification sending complete.");

        return true;
    }
}
