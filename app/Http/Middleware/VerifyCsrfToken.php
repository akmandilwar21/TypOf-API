<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        "/auth",
        "/addToCart",
        "/removeProductFromCart",
        "/addAddress",
        "/updateAddress",
        "/getCartDetails",
        "/getProductDetails",
        "/fetchAllProduct",
        "/fetchAllAddress",
        "/deleteAddress/*",
        "/checkUserExistence",
        "/verifyOtp",
        "/createUser",
        "/insertProductApi",
        "/updateProductApi/*",
        "/deleteProductApi/*",
        "/makeAddressDefault/*",
        "/webhook",
        "/sendMessage"
    ];
}
