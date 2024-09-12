<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignUpUserRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class SignUpController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignUpUserRequest $request)
    {
        $user = User::create($request->except('passowrd') + ['password' => Hash::make($request->password)]);
        event(new Registered(user: $user));

        return response()->json([
            'authToken' => $user->createToken($request->ip(), ['email-verification-token'])->accessToken,
            'user' => UserResource::make($user),
        ]);
    }
}
