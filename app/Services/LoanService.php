<?php
// app/Services/ClientProfileService.php
namespace App\Services;

use App\Models\Profiles;
use App\Models\Loans;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoanService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = "https://lending.nobellending.co.ke/fineract-provider/api/v1";
        $this->username = "nobel";
        $this->password = "nobel3047";
    }


    public function fetchLoanProducts()
    {

            try {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withOptions([
                        'verify' => true, // Enforce SSL certificate verification
                    ])
                    ->get($this->baseUrl . '/loanproducts', [
                        'sqlSearch' => "c.status=loanProduct.active"
                    ]);

              
   Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Response: {$response->body()} |Status :{$response->status()} ");

            if ($response->status() !== 200) {
                return ['success' => false, 'message' => 'API call failed'];
            }
    
            $products = $response->json();

            if (empty($products) ) {
                return ['success' => false, 'message' => 'No record found on Mifos'];
            }


return ['success' => true, 'message' => 'loan products exist' ,"loan_products"=>$products];
    } catch (\Exception $e) {
        report($e);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Error: {$e->getMessage()} ");


        return ['success' => false, 'message' => $e->getMessage()];
    }
    }

    public function applyloan($loan_amount,  $selected_loan,$interest,$totalPayable,$profile,$mifos_profile )
    {
        if(empty($selected_loan) || empty($mifos_profile) ||empty($profile))
        {
     return ['success' => false, 'message' =>"no profiles"];
        }
//print_r($mifos_profile);

        $payload = [
            "clientId"                      => (int)$mifos_profile['id'],
            "productId"                     => (int)$selected_loan['id'],
            "principal"                     => (float)$loan_amount,
            "loanTermFrequency"             => $selected_loan['repaymentEvery'],
            "loanTermFrequencyType"         => $selected_loan['repaymentFrequencyType']['id'], // 2 = Months
            "numberOfRepayments"            => $selected_loan['numberOfRepayments'],
            "repaymentEvery"                => $selected_loan['repaymentEvery'],
            "repaymentFrequencyType"        => $selected_loan['repaymentFrequencyType']['id'],
            "interestRatePerPeriod"         => ($selected_loan['interestRatePerPeriod'] )?? 0,
            "interestType"                  =>  $selected_loan['interestType']['id'] ?? null, // 0 = Declining balance
            "interestCalculationPeriodType" => $selected_loan['interestCalculationPeriodType']['id'] ?? null,
            "amortizationType"               => $selected_loan['amortizationType']['id'] ?? null,
            "charges"                       => $interest,
            "expectedDisbursementDate"      => Carbon::now()->addDay()->format('d F Y'),
            "submittedOnDate"               => Carbon::now()->format('d F Y'),
            "locale"                        => "en",
            "dateFormat"                    => "dd MMMM yyyy",
            "transactionProcessingStrategyId" =>  $selected_loan['transactionProcessingStrategyId'],
            "loanType"                      => "individual"
        ];
        echo json_encode($payload);
        // Step 5: Apply loan
        $response = Http::withBasicAuth($this->username, $this->password)
        ->withOptions([
            'verify' => true, // Enforce SSL certificate verification
        ])
        ->post("$this->baseUrl/loans", $payload);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->json()];
        }

        die("afda");
        ///we apply loan now 
    }

    private function generatePin($length = 4)
    {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
