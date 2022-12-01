<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Models as SquareModels;
use Square\Models\SearchCatalogObjectsRequest;

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
}
