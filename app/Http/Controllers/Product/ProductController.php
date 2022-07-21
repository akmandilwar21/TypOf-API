<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     *Method to get product details
     * @param  Product $productId,Request $request
     * @return json $response
     */
    public function getProductDetails(Product $productId,Request $request) {
        try {
            $product = Product::where("product_id", $productId->product_id)->where("store_id", $request["store_id"])->first()->toArray();
            $response = array(
                "message" => "success",
                "status" => 200,
                "product" => $product
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get product details
     * @param Request $request
     * @return json $response
     */
    public function fetchAllProduct(Request $request) {
        try {
            $products = Product::where("store_id", $request["store_id"])->get()->toArray();
            $response = array(
                "message" => "success",
                "status" => 200,
                "product" => $products
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
