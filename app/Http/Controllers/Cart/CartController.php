<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;

class CartController extends Controller
{
    /**
     *Method to add products in cart
     * @param  Request  $request
     * @return json Response
     */
    public function addToCart(Request $request) {
        try {
            $product = Product::where("product_id", $request["product_id"])->first();

            if(!$product) {
                return response()->json(["message" => "Product Not Found"], 404); 
            }

            $existingCart = Cart::where('cid', $request["customer_id"])->first();
            if($existingCart && array_key_exists($request["product_id"],json_decode($existingCart->cart, true))) {
                return response()->json(["message" => "Product already in cart"], 200);
            }
            $productDetailsJson = $this->productDetailsJsonFormat($product, $request);
            $matchThese = array("cid"=>$request["customer_id"]);
            $cart = array("cid"=>$request["customer_id"],"cart"=>$productDetailsJson,"abandon_status"=>"0");
            Cart::updateOrCreate($matchThese,$cart);

            return response()->json(["message" => "Added to cart successfully"], 200); 
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to create json format for product details
     * @param  $product and $request
     * @return json
     */
    public function productDetailsJsonFormat($product, $request) {
        try {
            $productArray=array(
                $product->product_id => array(
                    "pname"=> $product->product_name,
                    "quantity"=> $request->quantity,
                    "price"=> $product->price,
                    "image"=> $product->image,
                    "slug"=> $product->slug,
                    "size"=> [],
                    "selected_size"=> $request->selected_size,
                    "shipping_cost"=> $product->shipping_cost,
                    "pid"=> $product->product_id,
                    "weight"=> "",
                )
            );
            $existingCart = Cart::where("cid", $request["customer_id"])->first();
            if($existingCart) {
                $productArray = json_decode($existingCart->cart, true) + $productArray;
                return json_encode($productArray);
            } else {
                return json_encode($productArray);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to remove products from cart or empty cart or manipulate the quantity of cart
     * @param  Request  $request
     * @return json Response
     */
    public function removeProductFromCart(Request $request) {
        try {
            $productId = $request["product_id"];
            $cart = Cart::where("cid", $request["customer_id"])->first();
            if(!$cart) {
                $message = "Cart is empty for this Customer";
                $statusCode = 404;
            } else {
                if($request["action"] == "empty") {
                    $cart->delete();
                    $message = "All products removed from the cart";
                    $statusCode = 200;
                } 
                else {
                    $cartDetails = json_decode($cart->cart, true);
                    if (!array_key_exists($productId,$cartDetails)) {
                        $message = "Product not found in cart";
                        $statusCode = 404;
                    } else {
                        if($request["action"] == "remove_product") {
                            $this->removeProduct($cartDetails, $cart, $productId);
                            $message = "Product removed from the cart";
                            $statusCode = 200;
                        } elseif($request["action"] == "add_quantity") {
                            $this->increaseProductQuantity($cartDetails, $cart, $productId);
                            $message = "Product quantity increased by 1";
                            $statusCode = 200;
                        } elseif($request["action"] == "sub_quantity") {
                            $this->decreaseProductQuantity($cartDetails, $cart, $productId);
                            $message = "Product quantity decreased by 1";
                            $statusCode = 200;
                        }
                    }
                }
            }
            return response()->json(["message" => $message], $statusCode);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to remove products from cart
     * @param  $cartDetails, $cart, $productId
     * @return void
     */
    public function removeProduct($cartDetails, $cart, $productId) {
        unset($cartDetails[$productId]);
        $cart->update(["cart"=>$cartDetails]);
    }

    /**
     *Method to increase quantity of product in cart
     * @param  $cartDetails, $cart, $productId
     * @return void
     */
    public function increaseProductQuantity($cartDetails, $cart, $productId) {
        $cartDetails[$productId]["quantity"] = (string)++$cartDetails[$productId]["quantity"];
        $cart->update(["cart"=>$cartDetails]);
    }

    /**
     *Method to increase quantity of product in cart
     * @param  $cartDetails, $cart, $productId
     * @return void
     */
    public function decreaseProductQuantity($cartDetails, $cart, $productId) {
        $cartDetails[$productId]["quantity"] = (string)--$cartDetails[$productId]["quantity"];
        if($cartDetails[$productId]["quantity"] == 0) {
            count($cartDetails) >> 1 ? $this->removeProduct($cartDetails, $cart, $productId) : $cart->delete();
        } else {
            $cart->update(["cart"=>$cartDetails]);
        }
    }

    /**
     *Method to get cart details
     * @param  Request $request
     * @return json $response
     */
    public function getCartDetails(Request $request) {
        try {
            $customer = Customer::where("customer_id", $request["customer_id"])->first();
            if(!$customer) {
                return response()->json(["message" => "Customer not found"], 404);
            }

            $cart = Cart::where("cid", $request["customer_id"])->first();

            $response = array(
                "message" => "sucsess",
                "status" => 200,
                "cart" => json_decode($cart->cart, true)
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
