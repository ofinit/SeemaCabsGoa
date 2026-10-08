<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateAccountDetailsRequest;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show()
    {
        return app(UserController::class)->details();
    }

    public function update(UpdateAccountDetailsRequest $request)
    {
        return app(UserController::class)->update($request);
    }

    public function delete(Request $request)
    {
        return app(UserController::class)->deleteAccount($request);
    }
}
