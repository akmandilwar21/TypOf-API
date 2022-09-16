<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserChatHistory;
use App\Models\ChatUsersLists;
use App\Models\Customer;

class WhatsappController extends Controller
{
    public function testWhatspp(Request $request) 
    {
        // $token = "gIHlGz584kwdpHCh";
        // $url = "https://api.chat-api.com/instance451649/sendMessage?token=".$token;
        $token = "EAAEozB15ZCF8BAJgz6B1ARXFrxgEHSykiC3H6UH358uThNqMwKieEx0X3oZBkVZC5qJOSE9CrwxawIZCbCDvvZBbSFKoxVNCGVYKlXr3bhVwkImzfqqqtBVJ6B7hEZBarmY6hHcYde5CihutrXtrSDYY0cV1ZC2cQKaCZCNDeBH1VpjZCkBBma5EsqFTDfuBFrZBGTEzn9ohKZBdWJBoC2vGulWZAZBZA7NVJThFkZD";
        $url = "https://graph.facebook.com/v14.0/103470409071258/messages";
        $ch = curl_init();
        $headers = array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/json', 
        );
        $data = [
            'messaging_product' => "whatsapp",
            "recipient_type" => "individual",
            'to' => "916200244082",
            'type' => "text",
            'text' => [
                "preview_url" => false,
                "body" => "Hello World"
            ],
        ];
        $body = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $data = curl_exec($ch);

        UserChatHistory::where("customer_id",$request["customer_id"])->update(['last_message'=>0]);
        $saveChatHistory=new UserChatHistory;
        $saveChatHistory->store_id = env("STORE_ID");
        $saveChatHistory->customer_id = $request["customer_id"];
        $saveChatHistory->customer_mobile = $request["customer_mobile"];
        $saveChatHistory->message = $request["message"];
        $saveChatHistory->last_message = 1;
        $saveChatHistory->sender = "store";
        $saveChatHistory->recipient = "customer";
        $saveChatHistory->save();
        return response()->json("Success");
    }

    /**
     *Method to get user chat lists
     * @param  
     * @return json Response
     */
    public function getChatUsersLists(Request $request) {
        try {
            $searchString = $request['search'];
            $chatUsersLists = ChatUsersLists::with(['userDetails'])
                                ->where("store_id" ,env("STORE_ID"))
                                ->where('customer_mobile', 'LIKE', "%$searchString%")
                                ->orWhereHas('userDetails', function ($query) use ($searchString){
                                    $query->where('customer_name', 'LIKE', "%$searchString%");
                                })
                                ->orderBy('created_at', 'desc')
                                ->get();
            if($chatUsersLists) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "chatUsersLists" => $chatUsersLists->toArray()
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No User Chat history found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get user lists for new chat
     * @param  
     * @return json Response
     */
    public function getUsersListsForNewChat(Request $request) {
        try {
            $searchString = $request['search'];
            $newChatUsersLists = Customer::where("store_id" ,env("STORE_ID"))
                                ->where('mobile', 'LIKE', "%$searchString%")
                                ->orWhere('customer_name', 'LIKE', "%$searchString%")
                                ->orderBy('customer_name')
                                ->get();
            if($newChatUsersLists) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "newChatUsersLists" => $newChatUsersLists->toArray()
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No Users found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *Method to get user lists for new chat
     * @param  
     * @return json Response
     */
    public function getUserChatHistory(Request $request) {
        try {
            $userChatHistory = UserChatHistory::where("store_id" ,env("STORE_ID"))
                                ->where('customer_id', $request["customer_id"])
                                ->orderBy('created_at', 'desc')
                                ->get();
            if($userChatHistory) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "userChatHistory" => $userChatHistory->toArray()
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No Users found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
