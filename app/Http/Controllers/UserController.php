<?php

namespace App\Http\Controllers;

use App\Models\UsedSignatures;
use App\Models\User;
use App\Models\UserPublicKey;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\PublicKey;

class UserController extends Controller
{

    public function get(Request $request) {

        if($request->header('X-user-public-key-id') == null || $request->header('X-signature') == null) {
            return response()->json(['error' => 'User public key or signature is missing in the headers.'], 422);
        }

        $signature = $request->header('X-signature');

        $usedSignature = UsedSignatures::where('signature', $signature)->first();

        /*
         * prevent reply attack
         */
        if($usedSignature != null) {
            return response()->json(['error' => 'Signature already used.'], 422);
        }
        UsedSignatures::create(['signature' => $signature]);

        $userPublicKey = UserPublicKey::findOrFail($request->header('X-user-public-key-id'));

        try {
            $decoded = (array) JWT::decode($signature, $userPublicKey->public_key, array('RS256'));
        }
        catch(\Exception $exception) {
            return response()->json(['error' => 'Public key is not valid for this signature.'], 422);
        }

        $timestamp = $decoded['timestamp'];

        if($timestamp + 60 < time()) {
            return response()->json(['error' => 'Signature expired.'], 422);
        }

        return response()->json($userPublicKey->user()->first());
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
            return response()->json(['error' => 'Secret public key is not correct.'], 422);
        }

        try {
            PublicKey::fromString($publicKey);
        }
        catch(\Exception $exception) {
            return response()->json(['error' => 'Public key is not valid.'], 422);
        }

        $userPublicKey = new UserPublicKey();
        $userPublicKey->user()->associate($user);
        $userPublicKey->public_key = $publicKey;
        $userPublicKey->save();

        return response()->json($userPublicKey);
    }

}
