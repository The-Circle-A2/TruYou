<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Spatie\Crypto\Rsa\PrivateKey;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function returnResponse($response) {

        $timestamp = time();

        $privateKey = PrivateKey::fromString(env('PRIVATE_KEY'));
        $signature = $privateKey->encrypt(hash('sha256', $response->getContent().$timestamp, true));

        return $response->header('X-signature', base64_encode($signature))
            ->header("Access-Control-Expose-Headers", "X-signature, X-timestamp")
            ->header('X-timestamp', $timestamp);
    }

    public function log($message) {

        $privateKey = PrivateKey::fromString(env('PRIVATE_KEY'));
        $timestamp = time();

        $signature = base64_encode($privateKey->encrypt(hash('sha256', $message.$timestamp, true)));

        $log = [
            'message' => $message,
            'signature' => $signature,
            'timestamp' => $timestamp
        ];

        $client = new Client();
        $res = $client->post(env('LOG_URL'), [
            RequestOptions::JSON => $log
        ]);
    }

}
