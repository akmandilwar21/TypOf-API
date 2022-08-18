<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RazorpayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

Route::group(["middleware" => ["checkAuth", "cors"]],function () {
    Route::post("/auth",[App\Http\Controllers\Admin\UserController::class, "index"]);
    Route::post("/checkUserExistence",[App\Http\Controllers\Admin\UserController::class, "checkUserExistence"]);
    Route::post("/verifyOtp",[App\Http\Controllers\Admin\UserController::class, "verifyOtp"]);
    Route::post("/createUser",[App\Http\Controllers\Admin\UserController::class, "createUser"]);

    Route::post("/addToCart",[App\Http\Controllers\Cart\CartController::class, "addToCart"]);
    Route::post("/removeProductFromCart",[App\Http\Controllers\Cart\CartController::class, "removeProductFromCart"]);
    Route::get("/getCartDetails",[App\Http\Controllers\Cart\CartController::class, "getCartDetails"]);

    Route::post("/addAddress",[App\Http\Controllers\Address\AddressController::class, "addAddress"]);
    Route::post("/updateAddress",[App\Http\Controllers\Address\AddressController::class, "updateAddress"]);
    Route::get("/getAddressDetails/{addressId}",[App\Http\Controllers\Address\AddressController::class, "getAddressDetails"]);
    Route::get("/fetchAllAddress",[App\Http\Controllers\Address\AddressController::class, "fetchAllAddress"]);
    Route::delete("/deleteAddress/{addressId}",[App\Http\Controllers\Address\AddressController::class, "deleteAddress"]);
    Route::post("/makeAddressDefault/{addressId}",[App\Http\Controllers\Address\AddressController::class, "makeAddressDefault"]);

    Route::get("/getProductDetails/{productId}",[App\Http\Controllers\Product\ProductController::class, "getProductDetails"]);
    Route::get("/fetchAllProduct",[App\Http\Controllers\Product\ProductController::class, "fetchAllProduct"]);
    // Route::get("/user/profile", function () {
    //     // Uses first & second middleware...
    // });
});


// insert Product Api to Google Merchant Centre
Route::post("/insertProductApi",[App\Http\Controllers\GoogleMerchantCenterController::class, "insertProductApi"]);
// get Product List Api from Google Merchant Centre
Route::get("/getProductListApi",[App\Http\Controllers\GoogleMerchantCenterController::class, "getProductListApi"])->name('getProductListApi');
// get Product List Api from Google Merchant Centre
Route::get("/getProductDetailsApi/{productId}",[App\Http\Controllers\GoogleMerchantCenterController::class, "getProductDetailsApi"]);
// get Product List Api from Google Merchant Centre
Route::post("/updateProductApi/{productId}",[App\Http\Controllers\GoogleMerchantCenterController::class, "updateProductApi"]);
// delete Product Api from Google Merchant Centre
Route::delete("/deleteProductApi/{productId}",[App\Http\Controllers\GoogleMerchantCenterController::class, "deleteProductApi"]);
Route::get("/getAccessToken",[App\Http\Controllers\GoogleMerchantCenterController::class, "getAccessToken"]);
Route::get("/getAuthCode",[App\Http\Controllers\GoogleMerchantCenterController::class, "getAuthCode"]);
Route::get("/test",[App\Http\Controllers\FacebookPixelController::class, "test"]);
Route::get("/createProduct",[App\Http\Controllers\FacebookPixelController::class, "createProduct"]);

//Below route for deleting catalogue and product in the catalogue
Route::get("/deleteFacebookProduct/{productId}",[App\Http\Controllers\FacebookPixelController::class, "deleteFacebookProduct"]);
//Below route to get catalogue and product details in the catalogue
Route::get("/getFacebookProduct/{productId}",[App\Http\Controllers\FacebookPixelController::class, "getFacebookProduct"]);
Route::get("/getProductList",[App\Http\Controllers\FacebookPixelController::class, "getProductList"]);
Route::get("/createCatalogue",[App\Http\Controllers\FacebookPixelController::class, "createCatalogue"]);
Route::get("/getCatalogueList",[App\Http\Controllers\FacebookPixelController::class, "getCatalogueList"]);