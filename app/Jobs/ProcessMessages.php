<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Messages;

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
Log::channel('pam_logs')->info("SendMessageJob|AAAAAAAAAAAAAAAAAAAAAAAAAAAAA|".json_encode($this->data));
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
			'username' => env("SMS_USERNAME"),
			'password' =>env("SMS_PASSWORD"),
			'shortcode'=> env("SMS_SHORTCODE"),
			'mobile'=> $msisdn,
			'message'=> $message
		];
   //     echo $sms['OTP_URL'].http_build_query($params);

		curl_setopt($curl, CURLOPT_URL, env("SMS_URL").http_build_query($params));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
        Log::channel('pam_logs')->info($preLogString ." |Send request| ".env("SMS_URL").http_build_query($params)."|=> Response =>".$curl_response);
$update = ['status_message'=>"message dispatched to network".$curl_response,"status"=>32];
        if (curl_errno($curl) != 0)
        {
            $update = ['status_message'=>"message dispatched to network".$curl_response,"status"=>3];

        }
        curl_close($curl);     
    //       $update_message = Messages::where('id','=',$this->date['message_id'])->update($update);
    } catch (Throwable $e) {
       Log::channel('pam_logs')->info("Send Message request |".json_encode($this->data)."| XXXERRORXXX|status to failed| Error Message => ".$th->getMessage());
       
    }

	  }

  
      
        }


    
}
