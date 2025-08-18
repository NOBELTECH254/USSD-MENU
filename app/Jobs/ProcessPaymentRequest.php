<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\GameSessions;
use App\Models\PlayerAccounts;

use Illuminate\Support\Facades\Log;
use App\Traits\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Hashids\Hashids;
class ProcessPaymentRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,MessageTrait;
    public $data;
    public $preLogString;
      
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        Log::channel('pam_logs')->info(" |ProcessPamJob received data".json_encode($data));
        $this->preLogString = "|ProcessPamJob ".json_encode($data)."|";

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
        $pam_request = GameSessions::where("id",$this->data['session_id'])->lockForUpdate()->first();
        if($pam_request->status <>1)
        {
            Log::channel('pam_logs')->info("$this->preLogString XXXXXX|ProcessPamJob received data".json_encode($this->data)."The status is not set for processing ".json_encode($pam_request));
            return ;
        }
	$token = $this->getAccessToken();

	$profile = PlayerAccounts::where('id', $pam_request->player_id )->firstorfail();
	Log::channel('pam_logs')->info("$this->preLogString XXXXXX|MMMMMMMMMMMMMMMMM ProcessPamJob Token".json_encode($token));
	    if(isset($token) and is_array($token) and array_key_exists("session_id",$token))
	    {
		    //send the payment request
 $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL,  env('PAYMENT_URL'));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); //setting custom header
	    $currentDateTime = Carbon::now();
	    $curl_post_data = [
    //Fill in the request parameters with valid values
    'api_username' => env("PAYMENT_USERNAME"),
'session_id' =>$token['session_id'],
    'route_name' => env('PAYMENT_ACTION'), 
    'debitor_name'=>"Customer",
    "debitor_account"=>$profile->msisdn,//$pam_request->player_id,
    "debitor_mobile"=>$profile->msisdn,//$pam_request->player_id,
    "debitor_currency_code"=>"KES",
    "debitor_country_code"=>"KE",
    "reference_number"=>$pam_request->ticket_code,
    "amount"=>$pam_request->bet_amount,
    "client_datetime"=>$currentDateTime->toIso8601String()
];
       $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
	    $curl_response = curl_exec($curl);
	                $httpstatus =curl_getinfo($curl, CURLINFO_HTTP_CODE);
            Log::channel('pam_logs')->info($this->preLogString ." |Send request| $data_string|=> Response =>".$curl_response."| http status $httpstatus");
            if (curl_errno($curl) != 0) :
                $pam_request->status = 3;
                $pam_request->status_message ="Error ".curl_error($curl);
                Log::channel('pam_logs')->error($this->preLogString ."sendCharge|Error sending xxxx ".curl_error($curl)."|".curl_errno($curl) );
               $pam_request->save();
$message = "Please try again to purchase your BET WINNING Ticket";
$this->SendMessage($pam_request->id,$profile->msisdn ,$message,"pam_request");
curl_close($curl);
DB::commit();

		return ;
           exit;
       endif;
       $results = json_decode($curl_response,true);
       switch($results['command_status'])
       {
       case "OK":
 $pam_request->status = 5;
 $pam_request->status_message="Pending Payment Authorization";
 $pam_request->transaction_id =$results['transaction_id'];
 $pam_request->save();
 $message = "Please Enter your PIN to Complete the Transaction to Purchase Ticket $pam_request->ticket_code , amount KES :$pam_request->bet_amount";

 $this->SendMessage($pam_request->id,$profile->msisdn ,$message,"pam_request");
	       break;
default;
 $pam_request->status = 3;
                $pam_request->status_message =$results['command_status'];
$pam_request->save();
$message = "Please try again to purchase your BET WINNING Ticket";
$this->SendMessage($pam_request->id,$profile->msisdn ,$message,"pam_request");

break;
       }
	    }
	    else
	    {
 Log::channel('pam_logs')->info("$this->preLogString XXXXXX |Failed to generate payment token");

		    $pam_request->status =3;
		    $pam_request->status_message ="Failed to generate payment token";
		    $pam_request->save();
	$message = "Please try again to purchase your BET WINNING Ticket";
$this->SendMessage($pam_request->id,$profile->msisdn ,$message,"pam_request");

	    }

        DB::commit();
    } catch (Exception $e) {
        // If something went wrong, rollback the transaction
        DB::rollback();
    
        // Log the error message
        Log::channel('pam_logs')->error("$this->preLogString XXXXXX |".$e->getMessage());
    
        // Return an error response to the user
        ﻿return ;
    }

    }

    private function getAccessToken() {
      
        try {
 // Retrieve token from cache
$token = Cache::get('specialsessionid');
if (!$token ) {
            // If token doesn't exist or is expired, refresh token
		$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('PAYMENT_SESSION_URL'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(  'Accept: application/json',
    'Content-Type: application/json')); //setting a custom header
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$curl_post_data = [
	'api_username' => env("PAYMENT_USERNAME"),
	"api_password"=>env("PAYMENT_PASSWORD")
];

        $data_string = json_encode($curl_post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            $result = curl_exec($ch);
	   $httpstatus =curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    Log::channel('pam_logs')->info("$this->preLogString |$data_string |". env('PAYMENT_SESSION_URL')."| GGGGGGGGGGGGGGGGGGGGGGGGGg Get Token Respone   ".$result."| http status $httpstatus");

                                if ($result === FALSE) {
                  return null;
}
            if (curl_errno($ch) != 0) {
                Log::channel('pam_logs')->info("$this->preLogString |||||||||||||||||||||||||| Get Token Respone   ".curl_errno($ch));

curl_close($ch);
return null;
            } else {
		    curl_close($ch);

            $token =json_decode($result,true);;
            if(isset($token) and is_array($token) and array_key_exists("session_id",$token))
            {
		     Cache::put('specialsessionid', $result, now()->addMinutes(10));
               return json_decode($result,true);
            }
            else
            {
                
                return null;
            }
            }
}
else
{
    return json_decode($token,true);
} 
} catch (Throwable $e) {
	Log::channel('pam_logs')->info("$this->preLogString |||||||||||||||||||||||||| Get Token Respone   ".$e->getMessage());
	return null;
            //return $result = json_encode($responseArray);
        }

    }

}
