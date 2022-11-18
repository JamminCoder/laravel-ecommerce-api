<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Products\ProductsController;
use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;

class PayPalController extends Controller
{
    public const BASE_URL = "https://api-m.sandbox.paypal.com";

    public static function createOrder($purchaseAmount) {
        $access_token = self::generateAccessToken();
        $url = self::BASE_URL . "/v2/checkout/orders";

        $client = new HttpClient([
            "headers" => [
                "Authorization" => "Bearer $access_token",
                "Content-Type" => "application/json",
            ]
        ]);
        
        $response = $client->post($url, [
            "body" => json_encode([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $purchaseAmount
                        ]
                    ]
                ],

                "application_context" => [
                    "brand_name" => "Zoar Valley Gifts & More",
                    "return_url" => "http://localhost:3000/#/shop/checkout"
                ]
            ])
        ]);

        return $response->getBody();
    }

    public static function capturePayment(Request $request) {
        $access_token = self::generateAccessToken();
        $orderID = $request->orderID;

        $url = self::BASE_URL . "/v2/checkout/orders/$orderID/capture";

        $client = new HttpClient([
            "headers" => [
                "Authorization" => "Bearer $access_token",
                "Content-Type" => "application/json",
            ]
        ]);

        $response = $client->post($url);
        if ($response->getStatusCode() === 201) ProductsController::removeStock($request->skus);
        return json_encode($response->getBody(), true);
    }

    public static function generateClientToken() {
        $access_token = self::generateAccessToken();
        $client = new HttpClient([
            "headers" => [
                "Authorization" => "Bearer $access_token",
                "Content-Type" => "application/json",
                "Accept-Language" => "en_US",
            ]
        ]);

        $response = $client->post(self::BASE_URL . "/v1/identity/generate-token");

        return $response->getBody();
    }

    public static function identity() {
        $access_token = self::generateAccessToken();

        $client = new HttpClient([
            "headers" => [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $access_token"
            ],
        ]);
        
        $response = $client->request(
            "GET",
            self::BASE_URL . "/v1/identity/oauth2/userinfo?schema=paypalv1.1"
        );

        return $response->getBody();
    }

    public static function generateAccessToken() {
        /**
         * curl -v -X POST "https://api-m.sandbox.paypal.com/v1/oauth2/token" \
            -u "<CLIENT_ID>:<CLIENT_SECRET>" \
            -H "Content-Type: application/x-www-form-urlencoded" \
            -d "grant_type=client_credentials"  
         */

        $client = new HttpClient();
        $response = $client->post(
            self::BASE_URL . "/v1/oauth2/token",
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
