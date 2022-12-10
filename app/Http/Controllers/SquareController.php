<?php

namespace App\Http\Controllers;

use App\Models\Product;
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


    public function orderCheckout(Request $request) {

        // {"products": ["PRODUCT_SKU_2022", "etc..."]}
        $products_json = json_decode($request->products_json, true);

        $client = self::client();
        
        // Get product SKUs from request and query database for product information
        // Store product info in $order_items
        $order_items = [];
        foreach ($products_json["products"] as $sku) {
            $product = Product::firstWhere("sku", $sku);
            error_log($product);
            array_push($order_items, [
                "name" => $product->name,
                "qty" => 1,
                "amount" => $product->price,
                "currency" => "USD",
            ]);
        }

        $line_items = [];
        foreach ($order_items as $item) {
            // Create new line items, store in $line_items

            $base_price_money = new \Square\Models\Money();
            $base_price_money->setAmount($item["amount"]);
            $base_price_money->setCurrency($item["currency"]);

            $order_line_item = new \Square\Models\OrderLineItem($item["qty"]);
            $order_line_item->setName($item["name"]);
            isset($item["note"]) ? $order_line_item->setNote($item["note"]): "";
            $order_line_item->setBasePriceMoney($base_price_money);

            array_push($line_items, $order_line_item);
        }


        
        $order = new SquareModels\Order(env('LOCATION_ID')); // New order
        $order->setLineItems($line_items); // Set order info

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
