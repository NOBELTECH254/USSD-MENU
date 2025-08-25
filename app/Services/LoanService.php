<?php
// app/Services/ClientProfileService.php
namespace App\Services;

use App\Models\Profiles;
use App\Models\Loans;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Traits\MessageTrait;

class LoanService
{
    use MessageTrait;

    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = env("MIFOS_URL");
        $this->username = env("MIFOS_USERNAME");
        $this->password = env("MIFOS_PASSWORD");
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
    
		$loanProducts = $response->json();
		//		print_r($loanProducts);
		//
	    $active_loan_product_ids= explode(',', env('ACTIVE_LOAN_PRODUCTS', ''));
$products = collect($loanProducts)->whereIn('id', $active_loan_product_ids)->values();
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
         // Step 5: Apply loan
        $response = Http::withBasicAuth($this->username, $this->password)
        ->withOptions([
            'verify' => true, // Enforce SSL certificate verification
        ])
        ->post("$this->baseUrl/loans", $payload);
Log::channel('ussd_log')->info("Apply Loan".json_encode($payload)." => Response =>".$response->body());
        if ($response->successful()) {
            $message = message_template('loan-application-success', ['amount'=>$loan_amount,"payable" =>$totalPayable,"due_date"=>""
        ]);

      
        $this->SendMessage($profile->id,$profile->mobile_number ,$message,"LOAN-REQUEST");
        $this->store_ussd(['mobile_number'=>$profile->mobile_number,"menu"=>"LOAN-REQUEST","request"=>"apply loan ".$profile->mobile_number. " Amount Ksh $loan_amount","response"=>$message,"request_data"=>[],"request_response"=>[]]);

            return ['success' => true, 'data' => $response->json()];
        } else {
            $message = message_template('loan-application-fail', ['amount'=>$loan_amount,"payable" =>$totalPayable,"due_date"=>""
        ]);
        $this->SendMessage($profile->id,$profile->mobile_number ,$message,"LOAN-REQUEST");

            return ['success' => false, 'error' => $response->json()];
        }

        ///we apply loan now 
    }


    public function payLoan($active_loans,$mifos_profile,$mobile_number,$amount )
    {
$loan_id =$active_loans['loan_ids'][0];
        try {
            $response = Http::withBasicAuth('mifosuser', 'mifospassword') // replace with real credentials
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(env("MIFOS_PAYMENT_URL"), [
                    
                        "mobile"=>$mobile_number,
                        "loanId"=>$loan_id,
                        "amount"=>$amount
                
		]);
	   Log::channel('ussd_log')->info("payloan live|".env("MIFOS_PAYMENT_URL")." |Send request| ".json_encode([
                        "mobile"=>$mobile_number,
                        "loanId"=>$loan_id,
                        "amount"=>$amount
                ])."|=> Response =>".$response->body());

            if ($response->successful() && isset($response['resourceId'])) {
                return "END Payment of KES {$amount} applied successfully to Loan #{$loan_id}";
            }
    
            return "END Payment not confirmed. Response: " . $response->body();
    
        } catch (\Exception $e) {
            return "END Payment failed: " . $e->getMessage();
        }
    }
    public function fetchLoans($mifos_profile)
    {

            try {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withOptions([
                        'verify' => true, // Enforce SSL certificate verification
                    ])
                    ->get($this->baseUrl . '/clients/'.$mifos_profile['id'].'/accounts', [
                      'fields' => 'loanAccounts'
                    ]);

              
   Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Response: {$response->body()} |Status :{$response->status()} ");

            if ($response->status() !== 200) {
                return ['success' => false, 'message' => 'API call failed'];
            }
    
            $products = $response->json();

            if (empty($products) ) {
                return ['success' => 201, 'message' => 'No record found on Mifos'];
            }

            $accounts = $response->json()['loanAccounts'] ?? [];
            $activeOrPending = array_filter($accounts, function ($loan) {
                return $loan['status']['active'] === true
                    || ($loan['status']['pendingApproval'] ?? false) === true;
            });

            $this->store_ussd(['mobile_number'=>$mifos_profile['mobileNo'],"menu"=>"FETCH-LOANS","request"=>"".$mifos_profile['mobileNo'],"response"=>json_encode($activeOrPending),"request_data"=>["profile_id"=>$mifos_profile['id']],"request_response"=>$activeOrPending]);

            if(isset($activeOrPending) and sizeOf($activeOrPending) > 0)
            return [
                'success' => true,
                'loanAccounts' => array_values($activeOrPending)
            ];
            else {
                return [
                    'success' => 201,
                    'message'=>"no loan"
                ];
            }

    } catch (\Exception $e) {
        report($e);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Error: {$e->getMessage()} ");
        return ['success' => false, 'message' => $e->getMessage()];
    }
    }
 public function fetchAffordability($mifos_profile)
    {

            try {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withOptions([
                        'verify' => true, // Enforce SSL certificate verification
                    ])
                    ->get($this->baseUrl . '/datatables/Affordability/'.$mifos_profile['id'], []);
   Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Response: {$response->body()} |Status :{$response->status()} ");

            if ($response->status() !== 200) {
                return ['success' => false, 'message' => 'API call failed'];
            }

            $amount = $response->json();

            if (empty($amount) ) {
                return ['success' => false, 'message' => 'No record found on Mifos'];
            }
	    if(isset($amount[0]['amount']))
	    	return [
                'success' => true,'amount'=>$amount[0]['amount']
            ];
            else {
                return [
                    'success' => 201,
                    'message'=>"no loan"
                ];
            }

    } catch (\Exception $e) {
        report($e);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Error: {$e->getMessage()} ");
        return ['success' => false, 'message' => $e->getMessage()];
    }
    }


    public function loansBalance($mifos_profile)
{
    try {
        //code...
        $response = Http::withBasicAuth($this->username, $this->password)
        ->withOptions([
            'verify' => true, // Enforce SSL certificate verification
        ])
        ->get($this->baseUrl . '/clients/'.$mifos_profile['id'].'/accounts', [
          'fields' => 'loanAccounts'
        ]);

        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Response: {$response->body()} |Status :{$response->status()} ");

        if ($response->status() !== 200) {
            return ['success' => false, 'message' => 'API call failed'];
        }

        $accounts = $response->json();



    $loans = $accounts['loanAccounts'] ?? [];

    // Step 2: Filter for active loans
    $activeLoans = array_filter($loans, function ($loan) {
        return $loan['status']['active'] ?? false;
    });

    if (empty($activeLoans)) {
        return [
            'success' => 201,
            'message' => 'No active loans found'
        ];
    }

    // Step 3: Get full loan details for each active loan
    $results = [];
    foreach ($activeLoans as $loan) {
        $response = Http::withBasicAuth($this->username, $this->password)
        ->withOptions([
            'verify' => true, // Enforce SSL certificate verification
        ])
        ->get($this->baseUrl . '/loans/'. $loan['id'], );
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Response: {$response->body()} |Status :{$response->status()} ");
        if ($response->status() == 200) {
        $details = $response->json();
       
        $results[] = [
            'loanId'      => $details['id'],
            'product'     => $details['loanProductName'],
            'principal'   => $details['principal'],
            'outstanding' => $details['summary']['totalOutstanding'],
            'disbursed'   => $details['disbursementDetails'][0]['netDisbursalAmount'] ?? null,
            'status'      => $details['status']
        ];
    }
}
if(sizeOf($results)> 0)
    return [
        'success' => true,
        'activeLoans' => $results
    ];
    else {
        return [
            'success' => false,
            'message' => "Loans are not available at the moment"
        ];
    }
} catch (Exception $e) {
    report($e);
    Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Error: {$e->getMessage()} ");
    return ['success' => false, 'message' => $e->getMessage()];
}
}

  
}
