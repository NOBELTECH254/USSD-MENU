<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MenuRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Hashids\Hashids;
class ProcessMenuRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $preLogString;
      
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        Log::channel('ussd_logs')->info(" |ProcessMenuRequests received data".json_encode($data));
        $this->preLogString = "|ProcessPayments ".json_encode($data)."|";

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            extract($this->data);
            $menu_requests = MenuRequests::create([
                'mobile_number' => $mobile_number,
                'menu' => $menu,
                'request'  => $request,
                'response' =>$response,
                "status"=>"success",
                'request_data'    => $request_data,
                'request_response'       => $request_response,
            ]);     
 Log::channel('ussd_logs')->info(" |ProcessMenuRequests received data".json_encode($data)."|Response =>".json_encode($response));
    } catch (Exception $e) {
        // If something went wrong, rollback the transaction
        DB::rollback();
        // Log the error message
        Log::channel('ussd_logs')->error("$this->preLogString XXXXXX |".$e->getMessage());
        // Return an error response to the user
        return ;
    }

    }



}
