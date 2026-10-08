<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Requests\Api\BookingRegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\OtpVerifyRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\SendForgotPasswordMailRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\Api\PasswordReset;
use App\Mail\Api\BookingRegisterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AuthController extends ResponseController
{
    public function register(RegisterRequest $request)
    {
        try {
            $requestData = $request->all();
            if ($request->social_login === Type::GOOGLE) {
                $requestData['password'] = randomPasswordFromEmail($requestData['email']);
            }
            $userData = User::store($requestData);
            if (!$userData) {
                return $this->error('Something went wrong, Please try again latter.');
            }

            Auth::login($userData);
            $token = $userData->createToken(Type::LoginToken)->plainTextToken;
            $user = $userData->only(['id', 'name', 'email', 'phone_number']);
            $data['user'] = $user;
            $data['token'] = $token;
            if ($request->token && $request->device) {

                UserFcmToken::where('device_id', $request->device)->delete();

                UserFcmToken::create([
                    'user_id' => $userData->id,
                    'device_id' => $request->device,
                    'token' => $request->token
                ]);

            }
            return $this->success($data, 'Account created successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of customer register :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
    public function bookingRegister(BookingRegisterRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['password'] = $requestData['email'];
            $userData = User::store($requestData);
            if (!$userData) {
                return $this->error('Something went wrong, Please try again latter.');
            }
            Auth::login($userData);
            $token = $userData->createToken(Type::LoginToken)->plainTextToken;
            $user = $userData->only(['id', 'name', 'email', 'phone_number']);
            $data['user'] = $user;
            $data['token'] = $token;
            try {
                $title = 'Account register during booking';
                Mail::to($request->email)->send(new BookingRegisterMail($user['name'], $requestData['password'], $title));
            } catch (\Throwable $th) {
                Log::error('Getting error of send mail for booking register :' . $th->getMessage());
            }
            return $this->success($data, 'Account created successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of customer register :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $userExist = User::where('email', $request->email)
                ->where('type', Type::CUSTOMER)
                ->first();

            if (($request->social_login === Type::GOOGLE || $request->social_login === Type::APPLE) && !$userExist) {
                $beforeAt = explode('@', $request->email)[0];
                $namePart = preg_replace('/[\._\-]/', ' ', $beforeAt);
                $name = ucwords(strtolower($namePart));
                $requestData = array(
                    'email' => $request->email,
                    'name' => $name,
                    'password' => $request->password,
                    'device' => $request->device,
                    'social_login' => $request->social_login
                );
                $userData = User::store($requestData);
                if (!$userData) {
                    return $this->error('Something went wrong, Please try again latter.');
                }
            }

            $user = User::where('email', $request->email)
                ->where('type', Type::CUSTOMER)
                ->first();

            if (!$user) {
                return $this->error('User not found.', 404);
            }
            // Check if user is active
            if ($user->status !== Type::ActiveUser) {
                return $this->error('Your account is inactive or suspended.', 403);
            }
            // If social login (e.g. Google), log in without password
            if ($request->social_login === Type::GOOGLE) {
                Auth::login($user);
            } elseif ($request->social_login === Type::APPLE) {
                Auth::login($user);
            } else {
                // Regular login with password
                if (
                    !Auth::attempt([
                        'email' => $request->email,
                        'password' => $request->password,
                        'type' => Type::CUSTOMER,
                        'status' => Type::ActiveUser,
                    ])
                ) {
                    return $this->error('Invalid credentials.', 400);
                }
            }

            $user = Auth::user();
            $token = $user->createToken(Type::LoginToken)->plainTextToken;

            $data['user'] = $user->only(['id', 'name', 'email', 'phone_number']);
            $data['token'] = $token;
            if ($request->token && $request->device) {

                UserFcmToken::where('device_id', $request->device)->delete();

                UserFcmToken::create([
                    'user_id' => $user->id,
                    'device_id' => $request->device,
                    'token' => $request->token
                ]);

            }

            return $this->success($data, 'Logged in successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of customer login :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function sendMail(SendForgotPasswordMailRequest $request)
    {
        try {
            $mail = $request->email;
            $user = User::where('email', $mail)->first();
            $title = 'Forgot password mail';
            $otp = rand(1111, 9999);
            $user->otp = $otp;
            $user->save();
            try {
                Mail::to($request->email)->send(new PasswordReset($user->name, $otp, $title));
            } catch (\Throwable $th) {
                Log::error('Getting error of send mail for forgot password :' . $th->getMessage());
            }
            return $this->success('', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of send forgot password mail :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function otpVerify(OtpVerifyRequest $request)
    {
        try {
            $mail = $request->email;
            $otp = $request->otp;
            $user = User::where('email', $mail)->first();
            if ($user->otp != $otp) {
                return $this->error('Invalid OTP.');
            }
            $user->otp = null;
            $user->save();
            return $this->success($mail, 'OTP verified successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of otp verify :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function passwordUpdate(UpdatePasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $user->password = $request->password;
            $user->save();
            return $this->success([], 'Password changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of update password :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
