<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->exists('website') || !$request->exists('api_key')) {
            return response()->json('Unauthorized User and Key is Missing');
        }
        $storeDetails = DB::table('store_table')->where('store_id', $request['store_id'])->first();
        if($storeDetails) {
            $requestAuthKey = $request['website'].$request['api_key'];
            $dbAuthKey  = md5($storeDetails->website).md5($storeDetails->api_key);
            if ($requestAuthKey != $dbAuthKey) {
                return response()->json('Unauthorized User');
            }
        }
        else {
            return response()->json('Store Not Found');
        }
        return $next($request);
    }
}
