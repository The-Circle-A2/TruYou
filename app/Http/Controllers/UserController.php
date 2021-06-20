<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\PrivateKey;

class UserController extends Controller
{

    public function get(Request $request, $username) {

        $user = User::where('username', $username)
            ->first();

        if($user == null) {
            $response = response()->json(['error' => 'User not found'], 404);
        }
        else {
            $response = response()->json($user);
        }

        $timestamp = time();

        $privateKey = PrivateKey::fromString(env('PRIVATE_KEY'));
        $signature = $privateKey->encrypt(hash('sha256', $response->getContent().$timestamp));

        return $response->header('X-signature', base64_encode($signature))
            ->header('X-timestamp', $timestamp);
    }

}
