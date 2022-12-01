<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Models as SquareModels;
use Illuminate\Support\Str;

class SquareController extends Controller
{
    private static function client() {
        return new SquareClient([
            'accessToken' => env('SQUARE_ENV', 'sandbox') === "production" 
                ? env('SQUARE_ACCESS_TOKEN', '')
                : env('SQUARE_SANDBOX_ACCESS_TOKEN', ''),

            'environment' => env('SQUARE_ENV', 'sandbox') === "production" 
                ? Environment::PRODUCTION
                : Environment::SANDBOX,
        ]);
    }

    public function testSquare() {
        $client = self::client();    
        
        try {
        
            $apiResponse = $client->getLocationsApi()->listLocations();
        
            if ($apiResponse->isSuccess()) {
                $result = $apiResponse->getResult();
                foreach ($result->getLocations() as $location) {
                    printf(
                        "%s: %s, %s, %s<p/>", 
                        $location->getId(),
                        $location->getName(),
                        $location->getAddress()->getAddressLine1(),
                        $location->getAddress()->getLocality()
                    );
                }
        
            } else {
                $errors = $apiResponse->getErrors();
                foreach ($errors as $error) {
                    printf(
                        "%s<br/> %s<br/> %s<p/>", 
                        $error->getCategory(),
                        $error->getCode(),
                        $error->getDetail()
                    );
                }
            }
        
        } catch (ApiException $e) {
            echo "ApiException occurred: <b/>";
            echo $e->getMessage() . "<p/>";
        }
    }

    public function getCatalog() {
        $client = self::client();
        $catalogApi = $client->getCatalogApi();

        $apiResponse = $catalogApi->listCatalog();

        if ($apiResponse->isSuccess()) {
            return $apiResponse->getResult();
        } else {
            return $apiResponse->getErrors();
        }
    }

    public static function getObject($objectID) {
        $client = self::client();

        $api_response = $client->getCatalogApi()->retrieveCatalogObject($objectID);

        if ($api_response->isSuccess()) {
            return $api_response->getResult();
        } else {
            return $api_response->getErrors();
        }
    }

    public function orderCheckout() {
        $client = self::client();
        $line_items = [];

        $order_items = [
            // Each item will be extracted from the order request
            [
                "name" => "Test Name",
                "qty" => 2,
                "amount" => 30000, // amount in cents (when USD)
                "note" => "Thank you!",
                "currency" => "USD",
            ]
        ];

        foreach ($order_items as $item) {
            $base_price_money = new \Square\Models\Money();
            $base_price_money->setAmount($item["amount"]);
            $base_price_money->setCurrency($item["currency"]);

            $order_line_item = new \Square\Models\OrderLineItem($item["qty"]);
            $order_line_item->setName($item["name"]);
            $order_line_item->setNote($item["note"]);
            $order_line_item->setBasePriceMoney($base_price_money);

            array_push($line_items, $order_line_item);
        }



        $order = new SquareModels\Order(env('LOCATION_ID'));
        $order->setLineItems($line_items);

        $body = new SquareModels\CreatePaymentLinkRequest();
        $body->setIdempotencyKey(Str::random());
        $body->setOrder($order);

        $api_response = $client->getCheckoutApi()->createPaymentLink($body);


        if ($api_response->isSuccess()) {
            return $api_response->getResult();
        } else {
            return $api_response->getErrors();
        }
    }
}
