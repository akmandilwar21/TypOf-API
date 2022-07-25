<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            return 'Yes!! Admin namespace is working successfully';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to check user existence
     * @param Request $request
     * @return json $response
     */
    public function checkUserExistence(Request $request) {
        try {
                $user = Customer::where("mobile", $request["mobile"])->where("store_id", $request["store_id"])->first();

                $customer = array(
                    "customerName" => "",
                    "customerEmail" => "",
                    "customerMobile" => $request["mobile"],
                    "message" => "User not found",
                    "status" => 404
                );

                if($user) {
                    $customer["customerName"] = $user->customer_name;
                    $customer["customerEmail"] = $user->email_id;
                    $customer["customerMobile"] = $user->mobile;
                    $customer["currentOtp"] = $this->sendOtp($user);
                    $user->otp = $customer["currentOtp"];
                    $customer["user"] = $user;
                    $customer["message"] = "User found";
                    $customer["status"] = 200;
                }
                return response()->json($customer);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to send otp to mobile number
     * @param $user
     * @return $otp
     */
    public function sendOtp($user) {
        try {
                $otp = rand(1000 , 9999);
                Customer::where("customer_id", $user->customer_id)->update(["otp"=>$otp]);
                return $otp;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to verify otp
     * @param $user
     * @return $otp
     */
    public function verifyOtp(Request $request) {
        try {
                $customer = Customer::where("customer_id", $request["customer_id"])->where("mobile", $request["mobile"])->where("otp", $request["otp"])->first();
                if($customer) {
                    $response = array(
                        "message" => "Otp verified successfully",
                        "status" => 200,
                        "customer" => $customer->toArray()
                    );
                    return response()->json($response);
                } else {
                    return response()->json(["message" => "Please enter correct otp"], 400);
                }
                return $otp;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to create user
     * @param Request $request
     * @return response
     */
    public function createUser(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|regex:/^[A-Za-z\s]{3,50}$/',
                'mobile' => 'required|unique:customer_table,mobile|regex:/^[6-9]{1}\d{9}$/',
                'email_id' => 'required|email|unique:customer_table,email_id'
            ]);
    
            if ($validator->fails()) {
                $responseArray = array(
                    'status' => "failure",
                    'status_code' => 422,
                    'message' => $validator->errors()->first(),
                );
                return response(json_encode($responseArray))->header('Content-Type', 'application/json');
            } else {
                $customer = new Customer;
                $customer->customer_name = $request["customer_name"];
                $customer->mobile = $request["mobile"];
                $customer->email_id = $request["email_id"];
                $customer->store_id = $request["store_id"];
                if($customer->save()) {
                    $response = array(
                        "message" => "User Created Successfully",
                        "status" => 200,
                        "customer" => $customer->toArray()
                    );
                    return response()->json($response);
                } else {
                    return response()->json(["message" => "Some error occured"], 500);
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
