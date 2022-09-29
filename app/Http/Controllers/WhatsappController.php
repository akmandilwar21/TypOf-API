<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserChatHistory;
use App\Models\ChatUsersLists;
use App\Models\Customer;
use App\Models\WhatsappTemplates;
use Log;

class WhatsappController extends Controller
{
    public function sendMessage(Request $request) 
    {
        try {
            $token = "EAAEozB15ZCF8BAEZCq1ATCgvVMbyPE4okJvGZBcN3qKjwft7vKe8dNLNpwmI4ntjYmE9qZACwAA3qzLnRiHj9lNF7SpVyAXBiU3VIIk72LXuM6iMZBlNZBRe3ttKyyO0poWMj1DjOdJsPz2ZCRoZCvq9dReOZCw0ffSFsRHd6aSTfrgsyXqrKnL9hyUuBbmuQv95rGA0pwCpJMFrZBtRgYpVEjfuIUH35mCNEZD";
            $url = "https://graph.facebook.com/v14.0/103470409071258/messages";
            $ch = curl_init();
            $headers = array(
                'Authorization: Bearer '.$token,
                'Content-Type: application/json', 
            );

            $data = [
                'messaging_product' => "whatsapp",
                'to' => "91".$request["customer_mobile"],
            ];
            if($request["user_type"] == "new_user") {
                $data['type'] = "template";
                $data["template"]["name"] = $request["template_name"];
                $data["template"]["language"]["code"] = "en_US";
            } elseif($request["user_type"] == "existing_user") {
                $data["recipient_type"] = "individual";
                $data["type"] = "text";
                $data["text"] = [
                    "preview_url" => false,
                    "body" => $request["message"]
                ];
            }
            $body = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $data = json_decode($data);
            
            if($data) {
                $message = $request["message"];
                $customerId = Customer::where("mobile", $request["customer_mobile"])->first()->toArray();
                if($request["user_type"] == "new_user") {
                    $messageTemplate = WhatsappTemplates::where("store_id" ,$request["store_id"])->where("template_name",$request["template_name"])->first()->toArray();
                    $message = $messageTemplate["template_body"];
                    $chatUserListInsert = ChatUsersLists::insert(["store_id" => $request["store_id"],
                                                    "customer_id" => $customerId['customer_id'],
                                                    "customer_mobile" => $request["customer_mobile"],
                                                    "last_message" => $message]);
                }
                UserChatHistory::where("customer_id",$customerId['customer_id'])->update(['last_message'=>0]);
                $saveChatHistory=new UserChatHistory;
                $saveChatHistory->store_id = env("STORE_ID");
                $saveChatHistory->customer_id = $customerId['customer_id'];
                $saveChatHistory->customer_mobile = $request["customer_mobile"];
                $saveChatHistory->message = $message;
                $saveChatHistory->last_message = 1;
                $saveChatHistory->sender = "store";
                $saveChatHistory->recipient = "customer";
                $saveChatHistory->whatsapp_chat_id = $data->messages[0]->id;
                $saveChatHistory->save();
                $response = array(
                    "message" => "success",
                    "status" => 200,
                );
                return response()->json($response);
            }  
        } catch (\Exception $e) {
            return $e->getMessage();
        }
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
            $userType = "new_user";
            $userChat = UserChatHistory::where("store_id" ,$request["store_id"])
                                ->where('customer_id', $request["customer_id"]);
            
            
            $userChatHistory = $userChat->orderBy('created_at', 'desc')
                                ->paginate($request["limit"])->toArray();
            
            $chatUserType = $userChat->where("sender", "customer")->count();
            if($chatUserType) {
                $userType = "existing_user";
            }
            $userChatHistory["user_type"] = $userType;
            if($userChat) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "userChatHistory" => $userChatHistory
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No Users found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function whatsappWebhook(Request $request)
    {
        if($request["hub_verify_token"] == env("WHATSAPP_TOKEN"))
        {
            return $request["hub_challenge"];
        }
    }

    public function whatsappWebhookAfter(Request $request) {
        try {
            if($request["entry"][0]["changes"][0]["value"]["messages"]) {
                $message = $request["entry"][0]["changes"][0]["value"]["messages"];
                // Log::info($request["entry"][0]["changes"][0]["value"]["messages"]);
                $customerId = Customer::where("mobile", substr($message[0]["from"],2))->first()->toArray();
                // Log::info($customerId);
                UserChatHistory::where("customer_id",$customerId['customer_id'])->update(['last_message'=>0]);
                $saveChatHistory=new UserChatHistory;
                $saveChatHistory->store_id = env("STORE_ID");
                $saveChatHistory->customer_id = $customerId['customer_id'];
                $saveChatHistory->customer_mobile = $message[0]["from"];
                $saveChatHistory->message = $message[0]["text"]["body"];
                $saveChatHistory->last_message = 1;
                $saveChatHistory->sender = "customer";
                $saveChatHistory->recipient = "store";
                $saveChatHistory->whatsapp_chat_id = $message[0]["id"];
                $saveChatHistory->save();

                $response = array(
                    "status" => 200
                );
                return response()->json($response);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getWhatsappTemplates(Request $request) {
        try {
            $whatsappTemplates = WhatsappTemplates::where("store_id" ,$request["store_id"])->get();
            if($whatsappTemplates) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "whatsappTemplates" => $whatsappTemplates->toArray()
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No Templates found for this store"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
