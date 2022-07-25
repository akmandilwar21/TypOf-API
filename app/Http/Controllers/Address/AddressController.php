<?php

namespace App\Http\Controllers\Address;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use GuzzleHttp\Client;
use App\Models\Cart;
use App\Models\Customer;

class AddressController extends Controller
{
    /**
     *Method to add address
     * @param  Request  $request
     * @return json Response
     */
    public function addAddress(Request $request) {
        try {

            $customer = Customer::where("customer_id", $request["customer_id"])->first();
            if(!$customer) {
                return response()->json(["message" => "Customer not found"], 404);
            }

            $gpd = $this->_place_detail($request['address1'].", ".$request['city']);
            if(empty($gpd)){
                return response()->json(["message" => "Wrong Address Entered."], 404);
            }

            $saveAddress=new Address;
            $saveAddress->address_location = json_encode($gpd);
            $saveAddress->customer_id = $request["customer_id"];
            $saveAddress->name = $request["name"];
            $saveAddress->mobile = $request["mobile"];
            $saveAddress->address1 = $request["address1"];
            $saveAddress->address2 = $request["address2"];
            $saveAddress->city = $request["city"];
            $saveAddress->state = $request["state"];
            $saveAddress->country = $request["country"];
            $saveAddress->pin = $request["pin"];
            if($saveAddress->save()) {
                Address::where('customer_id', $request["customer_id"])->first()->update(['default_address'=>0]);
                Address::where("address_id",$saveAddress->id)->update(['default_address'=>1]);
                Cart::where('cid', $request["customer_id"])->update(["checkout" => ['address'=>$saveAddress->id]]);
                return response()->json(["message" => "Address added successfully."], 200);
            }else{
                return response()->json(["message" => "Something Went Wrong."], 500);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get geographic address location
     * @param  $search
     * @return variable $geoLocation;
     */
    private function _place_detail($search)
    {
        $client = new Client();
        $result = $client->get("https://maps.googleapis.com/maps/api/geocode/json", [
            "query" => [
                "address" => $search,
                "key" => "AIzaSyCIoHM0Mu3Iv1WD4MWtmR_vTSkIssvAgNM"
            ],
        ]);
        $geoLocation = json_decode($result->getBody());
        if($geoLocation->status == "ZERO_RESULTS"){
            $geoLocation = null;
        }else{
            $geoLocation = json_decode($result->getBody())->results[0];
        }
        return $geoLocation;
    }

    /**
     *Method to update address
     * @param  Request  $request
     * @return json Response
     */
    public function updateAddress(Request $request) {
        try {
            $gpd = $this->_place_detail($request['address1'].", ".$request['city']);
            if(empty($gpd)){
                return response()->json(["message" => "Wrong Address Entered."], 404);
            }
            
            $saveAddress=Address::where("address_id", $request["address_id"])->update([
                'address_location' => json_encode($gpd),
                'customer_id' => $request["customer_id"],
                'name' => $request["name"],
                'mobile' => $request["mobile"],
                'address1' => $request["address1"],
                'address2' => $request["address2"],
                'city' => $request["city"],
                'state' => $request["state"],
                'country' => $request["country"],
                'pin' => $request["pin"]
            ]);
            
            if($saveAddress) {
                Address::where('customer_id', $request["customer_id"])->first()->update(['default_address'=>0]);
                Address::where("address_id",$request["address_id"])->update(['default_address'=>1]);
                Cart::where('cid', $request["customer_id"])->update(["checkout" => ['address'=>$request["address_id"]]]);
                return response()->json(["message" => "Address updated successfully."], 200);
            }else{
                return response()->json(["message" => "Something Went Wrong."], 500);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get address details
     * @param  Address $addressId,Request $request
     * @return json $response
     */
    public function getAddressDetails(Address $addressId,Request $request) {
        try {
            $customer = Customer::where("customer_id", $request["customer_id"])->first();
            if(!$customer) {
                return response()->json(["message" => "Customer not found"], 404);
            }
            $address = Address::where("address_id", $addressId->address_id)->where("customer_id", $request["customer_id"])->first();
            if($address) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "address" => $address->toArray()
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Address not found"], 404);
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get all addresses
     * @param Request $request
     * @return json $response
     */
    public function fetchAllAddress(Request $request) {
        try {
            $addresses = Address::where("customer_id", $request["customer_id"])->get();
            if($addresses) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "addresses" => $addresses->toArray()
                );
            } else {
                return response()->json(["message" => "Address not found"], 404);
            }
            
            return response()->json($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to delete address
     * @param  Address $addressId,Request $request
     * @return json $response
     */
    public function deleteAddress(Address $addressId,Request $request) {
        try {
            $customer = Customer::where("customer_id", $request["customer_id"])->first();
            if(!$customer) {
                return response()->json(["message" => "Customer not found"], 404);
            }
            $address = Address::where("address_id", $addressId->address_id)->where("customer_id", $request["customer_id"])->first();
            if($address) {
                $address->delete();
                $response = array(
                    "message" => "Address deleted successfully",
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Address not found"], 404);
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
