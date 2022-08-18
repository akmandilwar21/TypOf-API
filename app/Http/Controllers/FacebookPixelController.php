<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redirect;

class FacebookPixelController extends Controller
{
    // public function test(Request $request) {
    //     // $storeDetails = $this->getApiCallDetails();
    //     $url = "https://www.facebook.com/v14.0/dialog/oauth?client_id=774245743832960&redirect_uri=".env('FACEBOOK_PIXEL_REDIRECT_URL')."&scope=ads_management";
    //     return redirect($url);
    // }

    // public function getAccessToken($request) {
    //     $accessUrl = "https://graph.facebook.com/oauth/access_token?client_id=774245743832960&client_secret=80d1d20d03cfc3df090c1792619d2906&redirect_uri=".env('FACEBOOK_PIXEL_REDIRECT_URL')."&code=".$request['code'];
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $accessUrl);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    //     $data = curl_exec($ch);
    //     $token = json_decode($data, true);
    //     $storeDetails = Store::where("store_id", env('STORE_ID'))->first();
    //     $storeDetails->update(["facebook_access_token" => $token["access_token"]]);
    //     return $storeDetails->refresh();
    // }

    // public function getLongLivedAccessToken($accessToken) {
    //     $accessUrl = "https://graph.facebook.com/oauth/access_token?client_id=774245743832960&client_secret=80d1d20d03cfc3df090c1792619d2906&grant_type=fb_exchange_token&fb_exchange_token=".$accessToken;
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $accessUrl);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    //     $data = curl_exec($ch);
    //     $token = json_decode($data, true);
    //     // dd($token);
    //     $storeDetails = Store::where("store_id", env('STORE_ID'))->first();
    //     $storeDetails->update(["long_lived_facebook_access_token" => $token["access_token"], "facebook_token_expires_in" => $token["expires_in"]]);
    //     return $storeDetails->refresh();
    // }

    /**
     *Method to create catalogue under a business(businessID) in Facebook business
     * @param Request $request
     * @return json $response
     */
    public function createCatalogue(Request $request)
    {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").env("BUSINESS_ID")."/owned_product_catalogs?name=".$request["catalogue_name"];
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

    /**
     *Method to get catalogue list a catalogue in Facebook business
     * @param Request $request
     * @return json $response
     */
    public function getCatalogueList(Request $request)
    {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").env("BUSINESS_ID")."/owned_product_catalogs";
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
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
     *Method to get product list a catalogue in Facebook business
     * @param Request $request
     * @return json $response
     */
    public function getProductList(Request $request) {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").$request["catalogue_id"]."/products";
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
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
     *Method to create product under a catalogue in Facebook business
     * @param Request $request
     * @return json $response
     */
    public function createProduct(Request $request) {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").$request["catalogue_id"]."/products";
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
        );
        $data = [
            // 'mobile_link' => 'https://maavni.com/',
            // 'url' => "https://maavni.com/",
            // 'link' => 'https://maavni.com/',
            // 'currency' => 'INR',
            // 'price' => '100',
            // 'image_url' => 'https://maavni.com/',
            // 'retailer_id' => 'abhishek2603',
            // 'category' => 'decor',
            // 'description' => 'Created by APi',
            // 'name' => 'Test Feed product second',
            // 'gtin' => "08901030835902",
            // 'origin_country' => 'IN',
            'mobile_link' => $request["mobile_link"],
            'url' => $request["url"],
            'link' => $request["link"],
            'currency' => $request["currency"],
            'price' => $request["price"],
            'image_url' => $request["image_url"],
            'retailer_id' => $request["retailer_id"],
            'category' => $request["category"],
            'description' => $request["description"],
            'name' => $request["name"],
            'gtin' => $request["gtin"],
            'origin_country' => $request["origin_country"],
        ];
        $body = json_encode($data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        $data = json_decode($data);
        return response()->json($data);
    }

    /**
     *Method to get Catalogue and product in Facebook business
     * @param $productId(can be catalogue id also)
     * @return json $response
     */
    public function getFacebookProduct($productId) {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").$productId;
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
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
     *Method to delete Catalogue and product in Facebook business
     * @param $productId(can be catalogue id also)
     * @return json $response
     */
    public function deleteFacebookProduct($productId) {
        $t = env("Facbook_access_token");
        $url = env("FACEBOOK_URL").$productId;
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: Bearer ".$t,
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
