<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;

class PayPalController extends Controller
{
    public const BASE_URL = "https://api-m.sandbox.paypal.com";

    public static function createOrder(Request $request) {
        $access_token = self::generateAccessToken();
        $url = self::BASE_URL . "/v2/checkout/orders";
        $purchaseAmount = "100.00";

        $client = new HttpClient([
            "headers" => [
                "Authorization" => "Bearer $access_token",
                "Content-Type" => "application/json",
            ]
        ]);

        if (isset($request->card_number)) {
            $payment_source = [
                "card" => [
                    "number" => $request->card_number,
                    "expiry" => $request->expiration_date,
                    "name" => $request->name,
                    "billing_address" => [
                        "address_line_1" => $request->billing_address_street,
                        "address_line_2" => isset($request->billing_address_unit) ? $request->billing_address_unit : null,
                        "admin_area_1" => $request->billing_address_state,
                        "admin_area_2" => $request->billing_address_city,
                        "postal_code" => $request->billing_address_zip,
                    ]
                ]
            ];
        } else {
            $payment_source = null;
        }

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

                "payment_source" => $payment_source,
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
