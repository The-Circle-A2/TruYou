<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPublicKey;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function get(Request $request) {

        if($request->header('X-user-public-key-id') == null || $request->header('X-signature') == null) {
            return response()->json(['error' => 'User public key or signature is missing in the headers.']);
        }

        $userPublicKey = UserPublicKey::findOrFail($request->header('X-user-public-key-id'));



    }

    public function registerPublicKey(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'secret_public_key' => 'required'
        ]);

        $email = $request->input('email');
        $secret_public_key = $request->input('secret_public_key'); // based on HS256

        $user = User::where('email', $email)->firstOrFail();

        try {
            $decoded = (array) JWT::decode($secret_public_key, $user->master_password, ['HS256']);

            $publicKey = $decoded['public_key'];
        }
        catch(\Exception $exception) {
            return response()->json(['error' => 'Secret public key is not correct.']);
        }

        //TODO validate public key

        $userPublicKey = new UserPublicKey();
        $userPublicKey->user()->associate($user);
        $userPublicKey->public_key = $publicKey;
        $userPublicKey->save();

        return response()->json($userPublicKey);
    }

}
