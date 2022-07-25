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

Route::group(["middleware" => ["checkAuth"]],function () {
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

    Route::get("/getProductDetails/{productId}",[App\Http\Controllers\Product\ProductController::class, "getProductDetails"]);
    Route::get("/fetchAllProduct",[App\Http\Controllers\Product\ProductController::class, "fetchAllProduct"]);
    // Route::get("/user/profile", function () {
    //     // Uses first & second middleware...
    // });
});