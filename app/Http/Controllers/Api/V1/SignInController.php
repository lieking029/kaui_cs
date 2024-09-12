<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignInRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class SignInController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignInRequest $request)
    {
        $authenticatedUser = User::where('email', $request->email)->first();
        if(!$authenticatedUser) {
            return response()->json([
                'message' => 'credentials are incorrect, please try again'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->password, $authenticatedUser->password)) {
            return response()->json(['message' => 'Invalid Credentials.'], Response::HTTP_BAD_REQUEST);
        }

        $tokenType = 'authorized-token';
        $authenticatedUser->refresh();
        return response()->json([
            'authToken' => $authenticatedUser->createToken($request->ip(), [$tokenType])->accessToken,
            'user' => UserResource::make($authenticatedUser),
        ]);
    }
}
