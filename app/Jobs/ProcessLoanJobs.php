<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use App\Traits\MessageTrait;
class ProcessLoanJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,MessageTrait;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        Log::channel('ussd_logs')->info(" |ProcessLoanKobs received data".json_encode($data));

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
	    // process loan balance details

	 
    }

   

    
}
