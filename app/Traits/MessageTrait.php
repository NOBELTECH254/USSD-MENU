<?php
namespace App\Traits;
use App\Models\Messages;
use Illuminate\Support\Facades\Log;
trait MessageTrait
{
//SendMessage($pam_request->id,$profile->msisdn ,$message,"pam_request");    
	public function sendMessage($request_id,$msisdn,$message,$source)
    {
	  //  return;
        try{
            Log::channel('pam_logs')->info("Send Message request |$request_id,$message,$msisdn,$source");       
        $messageModel= Messages::create(
            [
            'source' => $source,
            "message_content"=>$message,
            "msisdn"=>$msisdn,
        "status"=>0,
        "status_message"=>"Queued for Sending"
        ]);

       if( $messageModel)
        {
            //Log::channel('pam_logs')->info("Send Message request |$request_id,$message,$msisdn,$source | created message ". $message->id);
           // \App\Jobs\ProcessMessages::dispatch(['message_id'=>$messageModel->id,"message"=>$message,"msisdn"=>$msisdn]);
            return response()->json([ "message" => "Message queued for sending . ID".$messageModel->id,"status" => true, "code" => 200,]);
        }
  return response()->json([ "message" => "cannot create message","status" => false, "code" => 422,],422);
      
    } catch (Throwable $th) {
       Log::channel('pam_logs')->info("Send Message request |$request_id,$message,$msisdn,$source XXXERRORXXX|status to failed| Error Message => ".$th->getMessage());
      return response()->json([ "message" => "cannot create message".$th->getMessage(),"status" => false, "code" => 422,],422);
    }
        
    }
}
