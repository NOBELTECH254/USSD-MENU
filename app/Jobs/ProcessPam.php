<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Deposits;
use App\Models\GameSessions;
use App\Models\PlayerAccounts;
use Illuminate\Support\Facades\Log;
use App\Traits\MessageTrait;
class ProcessPam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,MessageTrait;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        Log::channel('pam_logs')->info(" |ProcessPamJob received data".json_encode($data));

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
	    //
	   switch($this->data['action'])
       {
        case "confirm_ticket":
$this->confirmTicket();
            break;

            case "cancel_ticket":

                break;
       }
    }

    private function confirmTicket()
    {
        $deposit = GameSessions::findOrFail($this->data['deposit_id']);
        $game_session = GameSessions::findOrFail($this->data['session_id']);
        $game_session->status = 2;
        $game_session->status_message = "Confirming ticket to betstack";
        $game_session->save();
        $deposit->status = 2;
        $deposit->status_message = "Confirming ticket to betstack";
        $deposit->save();
//
	    $headers = [
        'accept: application/json',
        'operator-name: sportsloth',
        'timestamp:1710744679',
        'signature:7e0036893e60ba1951d382fcbf60e5b12ea22ff784fdea6292a2da0eaab05f0f'
	    ];
try
{
 $curl = curl_init();
 $url = "https://api.betstack.io/be/tickets/".$game_session->ticket_id."/paid";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); //setting custom header
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
        //    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response = curl_exec($curl);
            $information = curl_getinfo($curl);
            $httpstatus =curl_getinfo($curl, CURLINFO_HTTP_CODE);
Log::channel('pam_logs')->info(" |Confirm ticket now |$url =>$curl_response |=> ".curl_getinfo($curl, CURLINFO_HTTP_CODE));
switch($httpstatus)
{
    case 200:
        $game_session->status = 1;
$game_session->status_message = "Confirmed ticket to betstack";
$game_session->save();
$deposit->status = 1;
$deposit->status_message = "Confirmed ticket to betstack";
$deposit->save();
$message = "Please try again to purchase your BET WINNING Ticket";
$message = "Your ticket ".$game_session->ticket_id . " is successful";
 $profile = PlayerAccounts::where('id', $game_session->player_id )->firstorfail();
$this->SendMessage($game_session->id,$profile->msisdn ,$message,"pam_request");

break;
default:

$game_session->status = 3;
$game_session->status_message = $curl_response?? "Failed confirm action ticket to betstack";
$game_session->save();
$deposit->status = 3;
$deposit->status_message =  $curl_response?? "Failed confirm action ticket to betstack";
$deposit->save();
break;
    }
}
catch (Throwable $e) {
    Log::channel('pam_logs')->info(" |Confirm ticket now |$url =>$curl_response |=> ".$e->getMessage());

    $game_session->status = 5;
    $game_session->status_message = "Error confirming request to betstack" .$e->getMessage();
    $game_session->save();
    $deposit->status = 5;
    $deposit->status_message = "Error confirming request to betstack".$e->getMessage();
    $deposit->save();
}

    }
}
