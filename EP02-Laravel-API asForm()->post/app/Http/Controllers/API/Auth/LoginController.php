<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\API\BaseController as BaseController;

class LoginController extends BaseController
{
    public function login(LoginRequest $request)
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope' => '*',
        ]);
        $arr = json_decode($response, true);
        if ($response->successful()){
            $array = array(
                'token_type' => $arr['token_type'],
                'accessTokenExpiration' => $arr['expires_in'],
                'accessToken' => $arr['access_token'],
                'refreshToken' => $arr['refresh_token']
            );
            return $this->sendResponse($array, 'User info retrieved successfully.');
        }
         return $this->sendResponse($arr, 'Server.error');
    }

    public function refresh(RefreshTokenRequest $request)
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'refresh_token' => $request->refresh_token,
        ]);
        return $this->sendResponse($response->json(), 'Retrieved successfully.');
    }

}
