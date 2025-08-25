<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Messages;
use Illuminate\Support\Facades\Http;
class ProcessMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data =$data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
Log::channel('ussd_log')->info("SendMessageJob|AAAAAAAAAAAAAAAAAAAAAAAAAAAAA|".json_encode($this->data));
        if(array_key_exists("message_id",$this->data) and array_key_exists("message",$this->data) and array_key_exists("msisdn",$this->data))
        {
            $preLogString ="SendMessage|".$this->data['message_id']."|".$this->data['msisdn']."|".$this->data['message']."|";
	    $msisdn =$this->data['msisdn'];
	    $message = $this->data['message'];
        try {
		  $curl = curl_init();
		//curl_setopt($curl, CURLOPT_HTTPHEADER);

		# send request to send otp via sms
		$params = [
			'sender_name'=> env("BULK_SENDER"),
			'mobile'=> $msisdn,
			'message'=> $message,
            "response_type"=>"json"
		];
   //     echo $sms['OTP_URL'].http_build_query($params);
   $response = Http::withHeaders([
    'h_api_key' => env("BULK_API_KEY"),
    'Content-Type' => 'application/json',
])->timeout(10) ->post(env("SMS_URL"), $params);
$curl_response = $response->json();
Log::channel('ussd_log')->info($preLogString ."|".env("SMS_URL")." |Send request| ".env("SMS_URL")."/".json_encode($params)."|=> Response =>".json_encode($curl_response));

if ($response->successful()) {
    
     Log::channel('ussd_log')->info($preLogString ." |Send request| ".env("SMS_URL").json_encode($params)."|=> Response =>".json_encode($curl_response));
}

    //       $update_message = Messages::where('id','=',$this->date['message_id'])->update($update);
    } catch (Throwable $e) {
       Log::channel('ussd_log')->info("Send Message request |".json_encode($this->data)."| XXXERRORXXX|status to failed| Error Message => ".$th->getMessage());
       
    }

	  }

  
      
        }


    
}
