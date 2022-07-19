<?php

namespace App\Http\Controllers\Portal;

use App\Models\Customer;
use App\Models\Cart;
use App\Models\Address;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function addAddress(Request $request)
    {
        if(!session('cid'))
        {
            session(['previouspage' => '/user/orders']);
            return redirect('/user/login');
        }else{
            $request['customer_id'] = session('cid');
            $gpd = $this->_place_detail($request['address1'].", ".$request['city']);
            if(empty($gpd)){
                return redirect()->back()->with('error', 'Wrong Address Entered.');
            }
            $saveAddress=new Address($request->all());
            $saveAddress->address_location = json_encode($gpd);
            if($saveAddress->save())
            {
                Address::where('customer_id', session('cid'))->first()->update(['default_address'=>0]);
                $saveAddress->default_address = 1;
                $saveAddress->update();
                Cart::where('cid', session('cid'))->update(["checkout" => ['address'=>$saveAddress->address_id]]);
                return redirect()->back()->with('success','Address added successfully');
            }else{
                return redirect()->back()->with('error','Somthing Went Wrong');
            }
        }
    }

    private function _place_detail($search)
    {
        $client = new Client();
        $result = $client->get("https://maps.googleapis.com/maps/api/geocode/json", [
            "query" => [
                "address" => $search,
                "key" => "AIzaSyCIoHM0Mu3Iv1WD4MWtmR_vTSkIssvAgNM"
            ],
        ]);
        $d = json_decode($result->getBody());
        if($d->status == "ZERO_RESULTS"){
            $d = null;
        }else{
            $d = json_decode($result->getBody())->results[0];
        }
        return $d;
    }
}
