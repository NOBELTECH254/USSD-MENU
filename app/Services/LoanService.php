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
$today = date('d F Y');
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
	    'loanOfficerId'=>$mifos_profile['staffId'],
            "loanType"                      => "individual",
'charges'                        => [
                    [
                        'chargeId' => 1, // You may want to make this dynamic
                        'amount' => 10, // You may want to make this dynamic
                        'dueDate' => $today
                    ]
                ]
	];
	$today = date('d F Y');
/*	$payload = [
                'clientId'                       =>  (int)$mifos_profile['id'],
                'productId'                      => (int)$selected_loan['id'],
                'disbursementData'               => [],
                'principal'                      => $loan_amount,
                'loanTermFrequency'              => '7',
                'loanTermFrequencyType'          => 0,
                'numberOfRepayments'             => 1,
                'repaymentEvery'                 => '7',
                'repaymentFrequencyType'         => 0,
                'interestRatePerPeriod'          => 3,
                'interestRateFrequencyType'      => 2,
                'amortizationType'               => $select_loan['amortizationType']['id'] ?? 1,
                'isEqualAmortization'            => false,
                'interestType'                   => $selected_loan['interestType']['id'] ?? 1,
                'interestCalculationPeriodType'  => $selected_loan['interestCalculationPeriodType']['id'] ?? 1,
                'allowPartialPeriodInterestCalcualtion' => true,
                'transactionProcessingStrategyId'=> $selected_loan['transactionProcessingStrategyId'] ?? 1,
            //    'linkAccountId'                  => $walletAccountId,
                'createStandingInstructionAtDisbursement' => true,
                'charges'                        => [
                    [
                        'chargeId' => 1, // You may want to make this dynamic
                        'amount' => 10, // You may want to make this dynamic
                        'dueDate' => $today
                    ]
                ],
                'locale'                         => 'en',
                'dateFormat'                     => 'dd MMMM yyyy',
                'loanType'                       =>  'individual',
                'expectedDisbursementDate'       => $today,
                'submittedOnDate'                => $today,
	];
*/
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

       // $this->SendMessage($profile->id,$profile->mobile_number ,$message,"LOAN-REQUEST");
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
    public function loanSummary($mifos_profile,$profile)
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

                $loans = $response->json()['loanAccounts'] ?? [];
    $active_loans = collect($loans)->where('status.code', 'loanStatusType.active')->first();
$message = "No active loan at the moment. Please try later";
        

            $this->store_ussd(['mobile_number'=>$mifos_profile['mobileNo'],"menu"=>"FETCH-LOANS","request"=>"".$mifos_profile['mobileNo'],"response"=>json_encode($active_loans),"request_data"=>["profile_id"=>$mifos_profile['id']],"request_response"=>$active_loans]);
    if (!empty($active_loans) ) {
    /* Dear [Customer Name], this is a summary of your most recent loan. As of [Date], your outstanding balance is $[XXX.XX]. Last payment of $[XXX.XX] was received on [MM/DD/YYYY]. For full repayment details, please contact us at [Phone Number]. – Nobel Lending Limited
    */


        $lastPaymentAmount = $active_loans['summary']['lastRepaymentAmount'] ?? null;
        $lastPaymentDate   = isset($active_loans['timeline']['lastRepaymentDate'])
            ? Carbon::createFromFormat('Y,m,d', implode(',', $active_loans['timeline']['lastRepaymentDate']))->format('m/d/Y')
            : null;

     
                $message = message_template('loans-summary', $replacements = [
            'customer_name' => $mifos_profile['displayName'] ?? 'Customer',
            'date'          => now()->format('M d, Y'),
            'outstanding'   => $active_loans['totalOutstanding'] ?? $active_loans['loanBalance'],
            'last_payment_amount'   => $lastPaymentAmount,
            'last_payment_date'=>$lastPaymentDate,
            'support_number'  => env('SUPPORT_NUMBER')
        ]
		);
	if(strlen($message) < 10)
		$message = "Please contact support for your loan summary on ".env('SUPPORT_NUMBER');
$this->SendMessage($profile->id,$profile->mobile_number ,$message,"loans-summary","LOAN-SUMMARY");
//$this->SendMessage($profile->id,$profile->mobile_number ,$message,"LOAN-SUMMARY");
            }
           

    } catch (Exception $e) {
        report($e);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |Error: {$e->getMessage()} ");
        return ['success' => false, 'message' => $e->getMessage()];
    }
    }
  
}
