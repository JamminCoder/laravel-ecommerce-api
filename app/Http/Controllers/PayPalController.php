<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;

class PayPalController extends Controller
{
    public static function identity() {
        $access_token = self::token();

        $client = new HttpClient([
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $access_token"
            ],
        ]);
        
        $response = $client->request(
            "GET",
            "https://api-m.sandbox.paypal.com/v1/identity/oauth2/userinfo?schema=paypalv1.1"
        );

        return $response->getBody();
    }

    public static function token() {
        /**
         * curl -v -X POST "https://api-m.sandbox.paypal.com/v1/oauth2/token" \
            -u "<CLIENT_ID>:<CLIENT_SECRET>" \
            -H "Content-Type: application/x-www-form-urlencoded" \
            -d "grant_type=client_credentials"  
         */

        $client = new HttpClient();
        $response = $client->post(
            "https://api-m.sandbox.paypal.com/v1/oauth2/token",
            [
                "body" => "grant_type=client_credentials",

                "auth" => [
                    env("PAYPAL_SANDBOX_CLIENT_ID"),
                    env("PAYPAL_SANDBOX_CLIENT_SECRET"),
                ]
            ]
        );

        $resBody = $response->getBody();

        return json_decode($resBody, true)["access_token"];
    }
}
