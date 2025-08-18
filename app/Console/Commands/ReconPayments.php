<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deposits;
use App\Models\GameSessions;
use Illuminate\Support\Facades\Log;

class ReconPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recon-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
	     $pendingRecords = GameSessions::where('status',5)->get();

        foreach ($pendingRecords as $record) {
            // Dispatch job or process record here
		$record->status =9;
//		$record->save();
		try
{
 $curl = curl_init();
$url = "https://collections.sarafupayment.com/collectionrefNumStatus";
$curl_post_data = [
        'api_username' => "sportsloth",
	"reference_number"=>$record->ticket_code
];
$data_string = json_encode($curl_post_data);
echo $data_string;
curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(  'Accept: application/json',
		    'Content-Type: application/json'));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
	    $curl_response = curl_exec($curl);
            $information = curl_getinfo($curl);
            $httpstatus =curl_getinfo($curl, CURLINFO_HTTP_CODE);
Log::channel('pam_logs')->info(" |Confirm ticket now |$url =>$curl_response |=> ".curl_getinfo($curl, CURLINFO_HTTP_CODE));
$response = json_decode($curl_response ,true);

print_r($response);
switch($response['command_status'])
{
    case "INVALID_REFERENCE_NUMBER":
        $record->status = 3;
$record->status_message = $response['command_status'];
//$record->save();
break;
case "OK";
/*{"api_username":"sportsloth","reference_number":"a745abb6003d4080a479cd56fc015fe9"}{"transaction_id":"ee20daa79d8c4618a76076d252ec2ba2","api_username":"sportsloth","command_status":"OK","transaction_status":"00029","transaction_amount":"10.0","sender_name":"Customer"}
 */
if($response['transaction_status'] =="S000")
{
	$record->status = 1;
$record->status_message = $response['command_status'];
$record->save();
//we send the results
    \App\Jobs\ProcessPam::dispatch(['action'=>"confirm_ticket",'deposit_id'=>$record->id,'session_id'=>$record->id]);
}
elseif( $response['transaction_status'] =="00029")
{
	      $record->status = 1;
$record->status_message = $response['transaction_status'];
$record->save();
}
break;
default:
/*
$record->status = 3;
$record->status_message = $curl_response;
$record->save();
 */break;
}
}
catch (Throwable $e) {
    Log::channel('pam_logs')->info(" |Confirm ticket now |$url =>$curl_response |=> ".$e->getMessage());
    
    $record->status = 5;
    $record->status_message = "Error confirming request to betstack" .$e->getMessage();
    $record->save();
}

	}

        $this->info(count($pendingRecords) . ' pending records processed.');
	    	    //
    }
}
