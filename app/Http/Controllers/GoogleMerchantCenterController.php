<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redirect;

class GoogleMerchantCenterController extends Controller
{   
    public function getApiCallDetails(){
        $client = new \Google\Client();
        $client->setAuthConfig(public_path().env("CLIENT_SECRET_PATH"));
        $client->addScope('https://www.googleapis.com/auth/content');
        $client->setRedirectUri('http://127.0.0.1:8000/getAccessToken');
        $client->setAccessType('offline');        
        $client->setIncludeGrantedScopes(true);
        $storeDetails = Store::where("store_id", env("STORE_ID"))->first()->toArray();
        $client->setAccessToken($storeDetails['access_token']);

        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($storeDetails['refresh_access_token']);
            $new_access_token = $client->getAccessToken();
            $storeDetails = Store::where("store_id", env('STORE_ID'))->first();
            $storeDetails->update(["access_token" => $new_access_token["access_token"], "refresh_access_token" => $new_access_token["refresh_token"], "token_created_at" => date("Y-m-d h:i:s",$new_access_token["created"])]);
            return $storeDetails->refresh();
        }
        return $storeDetails;
    }
    public function getAccessToken(Request $request){
        $client = new \Google\Client();
        $client->setAuthConfig(public_path().env("CLIENT_SECRET_PATH"));
        $client->addScope('https://www.googleapis.com/auth/content');
        $client->setRedirectUri('http://127.0.0.1:8000/getAccessToken');
        $client->setAccessType('offline');   
        $client->setIncludeGrantedScopes(true);
        $accesToken = $client->authenticate($request["code"]);
        Store::where("store_id", env('STORE_ID'))->update(["auth_code" => $request["code"], "access_token" => $accesToken["access_token"], "refresh_access_token" => $accesToken["refresh_token"], "token_created_at" => date("Y-m-d h:i:s",$accesToken["created"])]);
        return redirect()->route('getProductListApi');

    }

    public function getAuthCode(){
        $client = new \Google\Client();
        $client->setAuthConfig(public_path().env("CLIENT_SECRET_PATH"));
        $client->addScope('https://www.googleapis.com/auth/content');
        $client->setRedirectUri('http://127.0.0.1:8000/getAccessToken');
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        return Redirect::to($client->createAuthUrl());

    }
    /**
     *Method to get Product List from Google Merchant Center
     * @return json $response
     */
    public function getProductListApi() {
        $storeDetails = $this->getApiCallDetails();
        $url = "https://shoppingcontent.googleapis.com/content/v2.1/".$storeDetails["google_merhcant_id"]."/products?merchantId=".$storeDetails["google_merhcant_id"]."&maxResults=10";
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$storeDetails["access_token"],
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

    /**
     *Method to get Product Details from Google Merchant Center
     * @param $productId
     * @return json $response
     */
    public function getProductDetailsApi($productId) {
        $storeDetails = $this->getApiCallDetails();
        $url = "https://shoppingcontent.googleapis.com/content/v2.1/".$storeDetails["google_merhcant_id"]."/products/".$productId;
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$storeDetails["access_token"],
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

    /**
     *Method to insert new Product in Google Merchant Center
     * @param Request $request
     * @return json $response
     */
    public function insertProductApi(Request $request) {
        $storeDetails = $this->getApiCallDetails();
        $url = "https://shoppingcontent.googleapis.com/content/v2.1/".$storeDetails["google_merhcant_id"]."/products";
        $data = [
            "offerId" => $request["offerId"],
            "title" => $request["title"],
            "description" => $request["description"],
            "link" => $request["link"],
            "imageLink" => $request["imageLink"],
            "contentLanguage" => "EN",
            "targetCountry" => "IN",
            "channel" => "online",
            "adult" => false,
            "kind" => "content#product",
            "brand" => $request["brand"],
            "googleProductCategory" => $request["googleProductCategory"],
            "gtin" => $request["gtin"],
            "price" => [
              "value" => $request["price"],
              "currency" => "INR"
            ],
            "productWeight" => [
              "value" => $request["productWeight"],
              "unit" => "kg"
            ],
            "shipping" => [
              "price" => [
                "value" => $request["shipping_price"],
                "currency" => "INR"
              ],
            ],
            "productTypes" => ["test"],
            "identifierExists" => true,
            "availability" => "on stock",
            "condition" => "new",
          ];
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$storeDetails["access_token"],
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $body = json_encode($data);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

    /**
     *Method to update Product in Google Merchant Center
     * @param Request $request
     * @return json $response
     */
    public function updateProductApi(Request $request, $productId) {
        $storeDetails = $this->getApiCallDetails();
        $url = "https://shoppingcontent.googleapis.com/content/v2.1/".$storeDetails["google_merhcant_id"]."/products/".$productId;
        $data = [
            "title" => $request["title"],
            "description" => $request["description"],
            "link" => $request["link"],
            "imageLink" => $request["imageLink"],
            "adult" => false,
            "kind" => "content#product",
            "brand" => $request["brand"],
            "googleProductCategory" => $request["googleProductCategory"],
            "gtin" => $request["gtin"],
            "price" => [
              "value" => $request["price"],
              "currency" => "INR"
            ],
            "productWeight" => [
              "value" => $request["productWeight"],
              "unit" => "kg"
            ],
            "shipping" => [
              "price" => [
                "value" => $request["shipping_price"],
                "currency" => "INR"
              ],
            ],
            "productTypes" => ["test"],
            "identifierExists" => true,
            "availability" => "on stock",
            "condition" => "new",
          ];
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$storeDetails["access_token"],
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $body = json_encode($data);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

     /**
     *Method to delete Product from Google Merchant Center
     * @param Request $request
     * @return json $response
     */
    public function deleteProductApi($productId) {
        $storeDetails = $this->getApiCallDetails();
        $url = "https://shoppingcontent.googleapis.com/content/v2.1/".$storeDetails["google_merhcant_id"]."/products/".$productId;
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$storeDetails["access_token"],
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }
}
