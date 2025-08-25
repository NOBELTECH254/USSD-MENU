<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use App\Traits\MessageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Hashids\Hashids;
use App\Services\LoanService;

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
        Log::channel('ussd_log')->info(" |ProcessPayments received data".json_encode($data));
        $this->preLogString = "|ProcessPayments ".json_encode($data)."|";

    }

    /**
     * Execute the job.
     */
    public function handle(LoanService $loanService): void
    {
        try {
            extract($this->data);
            //    \App\Jobs\ProcessMessages::ProcessPaymentRequest(['active_loans'=>$active_loans,'mifos_profile'=>$mifos_profile,'mobile_number'=>$this->_msisdn,'amount'=>$active_loans['totalOutstanding']]);
//    public function payLoan($active_loans,$mifos_profile,$mobile_number,$$ )
            $pay_loan = $loanService->payLoan($active_loans,$mifos_profile,$mobile_number,$amount);
 Log::channel('ussd_log')->info(" |ProcessPayments received data".json_encode($this->data)."|Response =>".json_encode($pay_loan));
    } catch (Exception $e) {
        // If something went wrong, rollback the transaction
        DB::rollback();
        // Log the error message
        Log::channel('ussd_log')->error("$this->preLogString XXXXXX |".$e->getMessage());
        // Return an error response to the user
        return ;
    }

    }



}
