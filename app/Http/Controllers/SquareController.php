<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Models as SquareModels;


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

    public function createOrder() {
        $client = self::client();

        $ordersApi = $client->getOrdersApi();

        $body = new SquareModels\CreateOrderRequest();
        $body_order_locationId = env('LOCATION_ID', '');
        $body->setOrder(new SquareModels\Order(
            $body_order_locationId
        ));
        $body->getOrder()->setReferenceId('my-order-001');
        $body_order_lineItems = [];

        $body_order_lineItems_0_quantity = '1';
        $body_order_lineItems[0] = new SquareModels\OrderLineItem(
            $body_order_lineItems_0_quantity
        );
        $body_order_lineItems[0]->setName('Test Item');
        $body_order_lineItems[0]->setBasePriceMoney(new SquareModels\Money());
        $body_order_lineItems[0]->getBasePriceMoney()->setAmount(1599);
        $body_order_lineItems[0]->getBasePriceMoney()->setCurrency(SquareModels\Currency::USD);

        $body_order_lineItems_1_quantity = '2';
        $body_order_lineItems[1] = new SquareModels\OrderLineItem(
            $body_order_lineItems_1_quantity
        );

        $body_order_lineItems_1_modifiers = [];

        $body_order_lineItems_1_modifiers[0] = new SquareModels\OrderLineItemModifier();
        
        $body_order_lineItems[1]->setModifiers($body_order_lineItems_1_modifiers);

        $body_order_lineItems_1_appliedDiscounts = [];

        $body_order_lineItems_1_appliedDiscounts_0_discountUid = 'one-dollar-off';
        $body_order_lineItems_1_appliedDiscounts[0] = new SquareModels\OrderLineItemAppliedDiscount(
            $body_order_lineItems_1_appliedDiscounts_0_discountUid
        );
        $body_order_lineItems[1]->setAppliedDiscounts($body_order_lineItems_1_appliedDiscounts);
        $body_order_lineItems[1]->

        $body->getOrder()->setLineItems($body_order_lineItems);

        $body_order_taxes = [];

        $body_order_taxes[0] = new SquareModels\OrderLineItemTax();
        $body_order_taxes[0]->setUid('state-sales-tax');
        $body_order_taxes[0]->setName('State Sales Tax');
        $body_order_taxes[0]->setPercentage('9');
        $body_order_taxes[0]->setScope(SquareModels\OrderLineItemTaxScope::ORDER);
        $body->getOrder()->setTaxes($body_order_taxes);

        $body_order_discounts = [];

        $body_order_discounts[0] = new SquareModels\OrderLineItemDiscount();
        $body_order_discounts[0]->setUid('labor-day-sale');
        $body_order_discounts[0]->setName('Labor Day Sale');
        $body_order_discounts[0]->setPercentage('5');
        $body_order_discounts[0]->setScope(SquareModels\OrderLineItemDiscountScope::ORDER);

        $body_order_discounts[1] = new SquareModels\OrderLineItemDiscount();
        $body_order_discounts[1]->setUid('membership-discount');
        
        $body_order_discounts[1]->setScope(SquareModels\OrderLineItemDiscountScope::ORDER);

        $body_order_discounts[2] = new SquareModels\OrderLineItemDiscount();
        $body_order_discounts[2]->setUid('one-dollar-off');
        $body_order_discounts[2]->setName('Sale - $1.00 off');
        $body_order_discounts[2]->setAmountMoney(new SquareModels\Money());
        $body_order_discounts[2]->getAmountMoney()->setAmount(100);
        $body_order_discounts[2]->getAmountMoney()->setCurrency(SquareModels\Currency::USD);
        $body_order_discounts[2]->setScope(SquareModels\OrderLineItemDiscountScope::LINE_ITEM);
        $body->getOrder()->setDiscounts($body_order_discounts);

        $body->setIdempotencyKey('8193148c-9586-11e6-99f9-28cfe92138cf');

        $apiResponse = $ordersApi->createOrder($body);

        if ($apiResponse->isSuccess()) {
            $createOrderResponse = $apiResponse->getResult();

            return [
                "orderResponse" => $createOrderResponse,
                "statusCode" => $apiResponse->getStatusCode(),
                "headers" => $apiResponse->getHeaders(),
            ];

        } else {
            return $apiResponse->getErrors();
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

    public function getCatalogInfo() {
        $client = self::client();
        $catalogApi = $client->getCatalogApi();

        $apiResponse = $catalogApi->catalogInfo();

        if ($apiResponse->isSuccess()) {
            return $apiResponse->getResult();
        } else {
            return $apiResponse->getErrors();
        }
    }
}
