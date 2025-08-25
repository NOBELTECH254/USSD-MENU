<?php
namespace App\Http\Controllers\Api;
use App\Models\Profiles;
use App\Models\Requests;
use App\Models\AccountSignups;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Hashids\Hashids;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Traits\MessageTrait;
use Carbon\Carbon;
class USSDController extends USSDControllerBase
       	{

          use MessageTrait;

      
public function gateway() {
		//we now start the menu  here
		// reset function to call so that we know when user has changed it
		//print_r($_SESSION);
		$functionToCall = $this->nextFunction;
		$this->nextFunction = '';
		$this->sessionState = 'CON';
		//trim input to remove trailing spaces
		$this->_input = trim($this->_input);

    if(empty($this->_msisdn ) || empty($this->sessionID))
		{
			echo $this->_msisdn;
			echo $this->sessionID;
			echo "here";
			$this->sessionState = "END" ;
			$this->displayText ="MENU Not loaded , please try later";
      $response =  $this->finalizeProcessing();
      /*
         {"sessionId":"ATUid_2a0dca9eae2c954a2a6d1c9d780f6b97","serviceCode":"*384*20099#","phoneNumber":"+254726742902","text":""}
       */
      return response($response , 200)->header('Content-Type', 'text/plain');
      
		}
    if($this->_input ==22)
	    $functionToCall = null;

					if ($functionToCall == '' || $functionToCall == null) { //if no next function defined, use startPage()
Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Startpage call");
			$this->startPage();
		}
		else {
			$returnresult = call_user_func(array($this, $functionToCall));
			Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|$functionToCall Input : {$this->_input} return result from custom function");
		}

		if ($this->sessionState == 'CON' && $this->nextFunction == '') {
			$this->nextFunction = $functionToCall;
		}
		/*  if ($this->nextFunction != '') {
		    $this->sessionState = 'CON';
		    }
		 */
		$response =  $this->finalizeProcessing();
		/*
		   {"sessionId":"ATUid_2a0dca9eae2c954a2a6d1c9d780f6b97","serviceCode":"*384*20099#","phoneNumber":"+254726742902","text":""}
		 */
		return response($response , 200)->header('Content-Type', 'text/plain');
	}
function startPage() {
        //Get profile and client profile details
	    //do a switch here
$this->displayText = "Welcome to Nobel Lending. \n1. Register\n2. View Terms and Conditions \n3 Know more";
$profile = $this->profileService->checkProfile($this->_msisdn);
/*if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";

  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile so end prematurely");

  return;
  //end menu prematurely
}
*/

$mifos_result = $this->profileService->fetchFromMifos($this->_msisdn);

  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |XXXXXXXXX|".json_encode($mifos_result)."|{$this->_msisdn}|{$this->sessionID}|Return an empty array for mifos_profile so create a new registration request");

//$mifos_result =  ['success'=>true,'profile'=>['name'=>'george']];
if(!$mifos_result)
{
  $this->displayText = "Welcome to Nobel Lending.\n 1. Register\n2. View Terms and Conditions \n3 Know more";
  $this->nextFunction = "Register";
  $this->previousPage = "";
  $this->sessionState = "CON";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for mifos_profile so end prematurely");
  return;
  //end menu prematurely
}

/*
1 if we no profile in the table but have a mifos table create the account for the person 
*/

if ($mifos_result['success'] ==false) {

  $this->displayText = "Welcome to Nobel Lending.\n1. Register\n2. View Terms and Conditions \n3 Know more";
  $this->nextFunction = "Register";
  $this->previousPage = "";
  $this->sessionState = "CON";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for mifos_profile so create a new registration request");
  return;
}
$mifos_profile = $mifos_result['profile'] ?? null;
if(!$mifos_profile )
{
$this->displayText = "Welcome to Nobel Lending.\n1. Register\n2. View Terms and Conditions \n3 Know more";
  $this->nextFunction = "Register";
  $this->previousPage = "";
  $this->sessionState = "CON";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for mifos_profile so create a new registration request");
  return;
}

if($profile['success'] ==false)
{
if($mifos_profile)
{
  //we now create a profile 
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Create Profile for user $this->_msisdn |Mifos Profile".json_encode($mifos_profile));

//  $mifos_result = $this->profileService->createProfile($mifos_profile,$this->_msisdn);
  $this->displayText = "Welcome to Nobel Lending.\n Your account has been created and you shall receive your PIN Number shortly ";
  $this->displayText = "By continuing, you agree with nobellending Terms and Conditions https://nobellending.co.ke/kwamua-biashara-t&cs/ \n1. Accept \n2. Decline";
  $this->nextFunction = "approveActivation";
  $this->previousPage = "";
  $this->sessionState = "CON";
  return;
}
else {
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account. Help? 0726397276";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for mifos_profile so create a new registration request");
  return;
}
}
$local_profile = $profile['profile'] ?? null;
if($local_profile && $mifos_profile)
{
//we save the profile on sessions 
$this->saveSessionVar("profile", $local_profile);
$this->saveSessionVar("mifos_profile", $mifos_profile);
//we have a profile check if the user also has a profile on the profiles database if not then create a pin number and exit the menu 
//we create it as a job
  $this->displayText = "Welcome to Nobel Lending. \nPlease enter your 4-digit PIN \n2. Help";
                $this->nextFunction = "mainMenu";
                $this->previousPage = "";
                $this->sessionState = "CON";
        $this->serviceDescription = "This is the first page of the experience!";
    }
  }
	  function approveActivation()
	  {
		  if($this->_input ==1)
		  {
			  $mifos_result = $this->profileService->fetchFromMifos($this->_msisdn);
$mifos_profile = $mifos_result['profile'] ?? null;
  $mifos_result = $this->profileService->createProfile($mifos_profile,$this->_msisdn);
		   $this->displayText = "Welcome to Nobel Lending.\n Your account has been created and you shall receive your PIN Number shortly ";
  $this->nextFunction = "approveActivation";
  $this->previousPage = "";
  $this->sessionState = "END";
  return;
		  }
	   if($this->_input ==2)
    {
      $this->displayText = "Go to https://nobellending.co.ke or contact our customer care at 0726397276 ";
      $this->nextFunction = "register";
      $this->previousPage = "";
      $this->sessionState = "END";
      return;
    }	
	  }
  function register()
  {
    $this->displayText = "Welcome to Nobel Lending.\n 1. Register\n2. View Terms and Conditions \n3 Know more";
    $this->nextFunction = "Register";
    $this->previousPage = "";
    $this->sessionState = "CON";

    $registration_steps =$this->getSessionVar("registration_steps") ??null;
if(!$registration_steps)
{
    if($this->_input ==1)
{

  $this->displayText = "By continuing, you agree with nobellending Terms and Conditions \n1. Accept \n2. Decline";
  $this->nextFunction = "register";
  $this->previousPage = "";
  $this->sessionState = "CON";
  $this->saveSessionVar("registration_steps", "toc_approve");

    
}
if($this->_input ==2)
{

  $this->displayText = "Our terms and conditions are https://nobellending.co.ke/kwamua-biashara-t&cs/";
  $this->nextFunction = "register";
  $this->previousPage = "";
  $this->sessionState = "END";
  $message = message_template('terms-template', [
]);
$this->SendMessage(0,$this->_msisdn ,$message,"TERMS-CONDITIONS");
}

if($this->_input ==3)
{
  $this->displayText = "Go to https://nobellending.co.ke or contact our customer care at 0726397276 ";
  $this->nextFunction = "register";
  $this->previousPage = "";
  $this->sessionState = "END";
}

}
else {


  # code...
  $this->displayText = "By continuing, your agree to share your data with Nobel Lending service \n1. Accept \n2. Decline";
  $this->nextFunction = "register";
  $this->previousPage = "";
  $this->sessionState = "CON";

  switch(strtolower($registration_steps))
{
  case "toc_approve":
    if($this->_input ==2)
    {
      $this->displayText = "Go to https://nobellending.co.ke or contact our customer care at 0726397276 ";
      $this->nextFunction = "register";
      $this->previousPage = "";
      $this->sessionState = "END";
      return;
    }
    if($this->_input ==1)
    {
    $this->displayText = "Enter your First and Last Names";
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "CON";
    $this->saveSessionVar("registration_steps", "name");
    $registration_steps =$this->getSessionVar("registration_steps") ??null;
 
    return;
    }
    else {
      $this->displayText = "Invalid Entry. By continuing, your agree to share your data with Nobel Lending service \n1. Accept \n2. Decline";
      $this->nextFunction = "register";
      $this->previousPage = "";
      $this->sessionState = "CON";
      $this->saveSessionVar("registration_steps", "toc_approve");
      return;
    }

    break;
case "name":
  $name = trim($this->_input);
  // Check if contains at least two words
  if (str_word_count($name) < 2) {
$name = null;
  }
  // Allow only letters, spaces, apostrophes, and hyphens
  /*
  if (!preg_match("/^[a-zA-Z' -]+$/", $name)) {
    $name = null;

  }*/

  if(!$name)
  {
    $this->displayText = "Invalid Name\n Enter your First and Last Names";
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "CON";
    $this->saveSessionVar("registration_steps", "name");
    $this->saveSessionVar("register_".$this->_msisdn,null);

    return;
  }
//we have a name we store 
$this->displayText = "Dear $name \n Please enter your National ID Number";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "national_id");
$this->saveSessionVar("register_".$this->_msisdn,['name'=>$name]);
return;
  
break;
case "national_id":
  $id = trim($this->_input);
  // Check if it's numeric
  if (!ctype_digit($id)) {
$id=null;
  }

  // Check length (5 to 8 digits)
  $length = strlen($id);
  if ($length < 5 || $length > 8) {
$id =null;
  }

if(!$id)
{

  $this->displayText = "Invalid ID  \nPlease enter your National ID Number";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "national_id");
return;
}

$registration  = $this->getSessionVar("register_".$this->_msisdn) ??null;
if(!$registration)
{
  $this->displayText = "Enter your Full Names";
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "CON";
    $this->saveSessionVar("registration_steps", "name");
    $this->saveSessionVar("register_".$this->_msisdn,null);
    return;
}
//we now save 
$this->displayText = "You must be 18 and above \nPlease enter your Year of Birth";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "year_of_birth");
$registration['national_id']=$id;
$this->saveSessionVar("register_".$this->_msisdn,$registration);
  break;


case "year_of_birth":

  $year = trim($this->_input);
  // Check if it's numeric
  if (!ctype_digit($year)) {
$year=null;
  }

  // Check length (5 to 8 digits)
  $length = strlen($year);
  if ($length <> 4) {
$year =null;
  }


if(!$year)
{

  $this->displayText = "You must be 18 and above \nPlease enter your Year of Birth e.g 2024";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "year_of_birth");
return;
}
if ((date("Y") - $year) <18 || (date("Y") - $year) > 70)
{
  $this->displayText = "You must be 18 and above \nPlease enter your Year of Birth e.g 2000";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "year_of_birth");
return;
}
$registration  = $this->getSessionVar("register_".$this->_msisdn) ??null;
if(!$registration)
{
  $this->displayText = "Enter your Full Names";
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "CON";
    $this->saveSessionVar("registration_steps", "name");
    $this->saveSessionVar("register_".$this->_msisdn,null);
    return;
}
//we now save 
$this->displayText = "Registration details \nName:".$registration['name']." \nID:".$registration['national_id']." \nYear of Birth:$year \n1. Register\n2. Cancel Registration";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "registration_confirm");
$registration['year']=$year;
$this->saveSessionVar("register_".$this->_msisdn,$registration);
  break;



case "registration_confirm":
  $this->displayText = "Registration end . Help ? 0726397276";
  $this->nextFunction = "register";
  $this->previousPage = "";
  $this->sessionState = "END";
 $registration  = $this->getSessionVar("register_".$this->_msisdn) ??null;
  if(!in_array($this->_input,[1,2]))
  {
$this->displayText = "Registration details \nName:".$registration['name']." \nID:".$registration['national_id']." \nYear of Birth:$year \n1. Register\n2. Cancel Registration";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "registration_confirm");
return;

	}
  if($this->_input ==1)
{
  $registration  = $this->getSessionVar("register_".$this->_msisdn) ??null;
  $required = ['name', 'national_id', 'year'];
$has_fields  =true;

  foreach ($required as $field) {
      if (!array_key_exists($field, $registration) || empty($registration[$field])) {
        $has_field = false;
      }
  }
  if($has_fields ==false)
  {
    $this->displayText = "Registration end . Help ? 0726397276";
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "END";
    return;
  }

  //we store the details in the registation table here and now 
  $model = AccountSignups::insertOrIgnore(
    [
    'mobile_number' => $this->_msisdn,
    "customer_name"=>$registration['name'],
    "status"=>0,
    "terms_approved"=>1,
    "national_id" =>$registration['national_id'],
    "year_of_birth" =>$registration['year'],
"created_at" =>Carbon::now(),
"updated_at"=>Carbon::now()
    ]
    );

    if($model)
    {
    $this->displayText = "Your account has been registered , you shall receive a call from our CS shortly  . Help ? 0726397276";
    }
    else {
      $this->displayText = "Registration failed . Help ? 0726397276";
    }
    $this->nextFunction = "register";
    $this->previousPage = "";
    $this->sessionState = "END";

} 

break;

default:
$this->displayText = "By continuing, your agree to share your data with Nobel Lending service \n1. Accept \n2. Decline";
$this->nextFunction = "register";
$this->previousPage = "";
$this->sessionState = "CON";
$this->saveSessionVar("registration_steps", "toc_approve");

break;

}

}
  }

    function mainMenu(){
switch($this->_input)
{
  case 2:
    $this->displayText = "Please contact the customer care number at  0726397276  ";
    $this->nextFunction = "startPage";
    $this->previousPage = "";
    $this->sessionState = "END";
    break;
    default:
$profile = json_decode($this->getSessionVar("profile")) ??null;
if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}

 // Check hashed PIN
 if (!Hash::check($this->_input, $profile->hashed_pin)) {
  $this->displayText = "Your PIN is incorrect. \nPlease re-enter your 4-digit PIN \n2. Help";
                $this->nextFunction = "mainMenu";
                $this->previousPage = "";
                $this->sessionState = "CON";
                return;
}
//we have a valid piin number now we give the second menu 

$this->displayText = "Nobel Lending, Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
$this->nextFunction = "loanMenu";
$this->previousPage = "";
$this->sessionState = "CON";
return;

    break;
}
         }
        


function loanMenu()
{

$this->displayText = "Invalid Entry, Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
$this->nextFunction = "loanMenu";
$this->previousPage = "";
$this->sessionState = "CON";

$profile = json_decode($this->getSessionVar("profile")) ??null;
if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}

switch($this->_input)
{
  case 1:
    //fetch loans
    $mifos_profile = $this->getSessionVar("mifos_profile");


$pending_active_loans = $this->loanService->fetchLoans($mifos_profile);
Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|pending_active_loans".json_encode($pending_active_loans));
if(empty($pending_active_loans))
{
  $this->displayText = "Thank you, Your loan application was not successful, contact support  ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
return;
}

if($pending_active_loans['success'] ==false)
{
  $this->displayText = "Thank you, Your loan application was not successful, contact support  ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
return;

}
if($pending_active_loans['success'] ===true)
{
  //active loans
  $this->displayText = "You have active loans that should be cleared first before applying for another loan  ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  return ;
}
/*
if($pending_active_loans['success'] ===201)
{
  $this->displayText = "Thank you, please contact support to confirm your loan eligibilty ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
return;
}
*/

    $this->displayText = "By continuing, you agree with Nobel Lending service terms and conditions \n1. Accept \n2. Decline";
    $this->nextFunction = "approveLoanRequest";
    break;
    case 2:
      $this->displayText = "My Account, Select \n1. View Loan Balance \n2. View Repayment History \n0. Home";
      $this->nextFunction = "accountMenu";
      break;
      case 3:
        $mifos_profile = $this->getSessionVar("mifos_profile")??null;

        $active_loans = $this->loanService->loansBalance($mifos_profile);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|active_loans".json_encode($active_loans));
        if($active_loans['success'] ==false)
        {
        
          $this->displayText = "Please try later to check your loan";
          $this->nextFunction = "loanPayment";
          $this->sessionState = "END";
          return;
        }
        if($active_loans['success'] ===201)
        {
          $this->displayText = "Your don't have any active loan at the moment";
          $this->nextFunction = "loanPayment";
          $this->sessionState = "END";
          return;
        }
        
        
        if($active_loans['success'] <> true )
        {
          $this->displayText = "Please try late to check your loan status";
          $this->nextFunction = "loanPayment";
          $this->sessionState = "END";
          return;
        }
        
        $totalPrincipal   = 0;
            $totalOutstanding = 0;
            $loanIds          = [];
        
        
            foreach ($active_loans['activeLoans'] as $loan) {
              $loanIds[] = $loan['loanId'];
              $totalPrincipal   += ceil($loan['principal']);
              $totalOutstanding += ceil($loan['outstanding']);
          }
           
        $this->saveSessionVar("active_loans",['loan_ids' =>$loanIds,"totalPrincipal"=>$totalPrincipal,"totalOutstanding"=>$totalOutstanding,"active_loans"=>$active_loans]);
        $this->displayText =  "CON Total Loan: KES {$totalPrincipal}\nOutstanding: KES {$totalOutstanding}\n1. Make Payment \n2. Make Partial Payment \n0 Home";
        $this->nextFunction = "loanPayment";
        break;
      case 4:
        $this->displayText = "Our Contact numbers are 0726397276 ";
        $this->sessionState = "END";
        break;
      case 0:
        $this->displayText = "Thank you for using Nobel Lending dial to access your NEXT LOAN ";
        $this->sessionState = "END";
        break;
        default:
        $this->displayText = "Invalid Entry, Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
$this->nextFunction = "loanMenu";
$this->previousPage = "";
$this->sessionState = "CON";
break;
}
}

function approveLoanRequest()
{
  switch($this->_input)
{
  case 1:
    //fetch loans
    $mifos_loans = $this->loanService->fetchLoanProducts();
    if(empty( $mifos_loans))
    {
    $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
    $this->nextFunction = "END";
    $this->previousPage = "";
    $this->sessionState = "END";
    Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
    return;
  }

  if( $mifos_loans['success']==false)
  {
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}
$loan_products = $mifos_loans['loan_products'];
  $mifos_profile = $this->getSessionVar("mifos_profile")??null;
$affordability = $this->loanService->fetchAffordability($mifos_profile);
$this->saveSessionVar("loan_products",json_encode($loan_products));
$menu ="";
foreach($loan_products as $index => $loan)
{
  $menu .= ($index + 1) . ". " . $loan['name'] . "\n";
}
    $this->displayText = "Apply Loan, Select \n$menu \n0. back";
    $this->nextFunction = "loanProductMenu";
    break;
    case 2:
        $this->displayText = "Thank you for using Nobel Lending dial  to access your NEXT LOAN ";
        $this->nextFunction = "END";
        break;
        default:
        $this->displayText = "Thank you for using Nobel Lending dial  to access your NEXT LOAN ";
        $this->nextFunction = "END";
break;
}
}

function loanProductMenu(){


  $mifos_profile = $this->getSessionVar("mifos_profile")??null;

  $loan_products = json_decode($this->getSessionVar("loan_products"),true) ??null;
  if(!$loan_products)
  {
    $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
    $this->nextFunction = "END";
    $this->previousPage = "";
    $this->sessionState = "END";
    Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
    return;
  }

    $menu ="";
    foreach($loan_products as $index => $loan)
    {
      $menu .= ($index + 1) . ". " . $loan['name'] . "\n";
    }
     $this->displayText = "Invalid Entry , Apply Loan, Select \n$menu \n0. back";
    $this->nextFunction = "loanProductMenu";
    $this->sessionState = "CON";
  
$profile = json_decode($this->getSessionVar("profile")) ??null;
if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}



switch($this->_input)
{

      case 0:

        $this->displayText = "Nobel Lending, Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
        $this->nextFunction = "loanMenu";
        $this->previousPage = "";
        $this->sessionState = "CON";

        break;
        default:

        if(!ctype_digit($this->_input))
{

  $menu ="";
  foreach($loan_products as $index => $loan)
  {
    $menu .= ($index + 1) . ". " . $loan['name'] . "\n";
  }
   $this->displayText = "Invalid Entry , Apply Loan, Select \n$menu \n0. back";
  $this->nextFunction = "loanProductMenu";
  $this->sessionState = "CON";
return;
}

        $loan_input = $this->_input - 1;
$mifos_profile = $this->getSessionVar("mifos_profile")??null;
$affordability = $this->loanService->fetchAffordability($mifos_profile);


        if (isset($loan_products[$loan_input])) { 
          $selected_loan = $loan_products[$loan_input];
	  $max_amount = $selected_loan['minPrincipal'];
	  if($affordability and $affordability['success']==true)
        $max_amount = $affordability['amount'];
	  $this->displayText = "Apply {$selected_loan['name']} , Reply the Loan Amount upto ".number_format($max_amount,0);

$this->saveSessionVar("selected_loan",json_encode($selected_loan));
$this->nextFunction = "loanAmountMenu";

$this->sessionState = "CON";
return;
        }



          $this->nextFunction = "loanMenu";
          $this->sessionState = "CON";
          return;
}



}

function loanAmountMenu(){


  $selected_loan = json_decode($this->getSessionVar("selected_loan"),true) ??null;

$mifos_profile = $this->getSessionVar("mifos_profile")??null;

  $affordability = $this->loanService->fetchAffordability($mifos_profile);

$max_amount = $selected_loan['minPrincipal'];
if($affordability and $affordability['success']==true)
	$max_amount = $affordability['amount'];

  $this->displayText = "Invalid Entry Apply {$selected_loan['name']} , Reply the Loan Amount upto KES ".number_format($max_amount,0);
      $this->nextFunction = "loanAmountMenu";
  $this->sessionState = "CON";


$profile = json_decode($this->getSessionVar("profile"),true) ??null;
if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}

if(!ctype_digit($this->_input))
{
  $this->displayText = "Invalid Entry Apply {$selected_loan['name']} , Reply the Loan Amount upto KES ".number_format($max_amount,0);
  $this->nextFunction = "loanAmountMenu";
$this->sessionState = "CON";
}
else {
  
$loan_amount =(int)$this->_input;
//$max_amount = (int)$selected_loan['maxPrincipal'];
if ( $loan_amount > $max_amount)
 {
  $this->displayText = "Invalid Amount Apply {$selected_loan['name']} , Reply the Loan Amount upto KES ".number_format($max_amount,0);
      $this->nextFunction = "loanAmountMenu";
  $this->sessionState = "CON";
}
else {
   //we have a valid amount we now apply the laon for the person
   $this->saveSessionVar("loan_amount",$loan_amount);

   $interestRate = $selected_loan['interestRatePerPeriod'] ?? 0;

   //$months = intval($selected_loan['interestRatePerPeriod']);
   $interest = ($loan_amount * ($interestRate / 100));
   $totalPayable = $loan_amount + ceil($interest);
 
   $this->displayText = "Confirm ".$selected_loan['name']." Loan Details \nAmount: Ksh".number_format($loan_amount,0)."\nInterest:".number_format(ceil($interest),0) ."\nTotal: Kes ".number_format($totalPayable)." \n Reply with \n1. Confirm\n2. Cancel";

   $this->nextFunction = "approveLoanMenu";
   $this->sessionState = "CON";
}
}
}


function approveLoanMenu(){
  $loan_amount =$this->getSessionVar("loan_amount");
  $selected_loan = json_decode($this->getSessionVar("selected_loan"),true) ??null;
  $interestRate = $selected_loan['interestRatePerPeriod'] ?? 0;
  //$months = intval($selected_loan['interestRatePerPeriod']);
  $interest = ($loan_amount * ($interestRate / 100));
  $totalPayable = $loan_amount + ceil($interest);
  $this->displayText = "Invalid Request. Confirm ".$selected_loan['name']." Loan Details\n Amount: Ksh".number_format($loan_amount,0)."\nInterest:".number_format(ceil($interest),0) ."\nFees:Ksh 0\nTotal: Kes ".number_format($totalPayable)." \n Reply with \n1. Confirm\n2. Cancel";
  $this->nextFunction = "approveLoanMenu";
  $this->sessionState = "CON";
$profile = json_decode($this->getSessionVar("profile")) ??null;
if(!$profile)
{
  $this->displayText = "Welcome to Nobel Lending.\n Please Contact customer care for activation of your account ";
  $this->nextFunction = "END";
  $this->previousPage = "";
  $this->sessionState = "END";
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|Return an empty array for profile");
  return;
}

switch($this->_input)
{
  case 1:
    $this->displayText = "Thank you, You shall receive a loan confirmation shortly  ";
    $this->nextFunction = "END";
    $this->previousPage = "";
    $this->sessionState = "END";
//we can apply the loan here 

$loan_amount =$this->getSessionVar("loan_amount");
  $selected_loan = json_decode($this->getSessionVar("selected_loan"),true) ??null;
  $interestRate = $selected_loan['interestRatePerPeriod'] ?? 0;
  //$months = intval($selected_loan['interestRatePerPeriod']);
  $interest = ($loan_amount * ($interestRate / 100));
  $totalPayable = $loan_amount + ceil($interest);
$profile = $this->getSessionVar("profile") ??null;

$mifos_profile = $this->getSessionVar("mifos_profile")??null;

$apply_loan = $this->loanService->applyloan($loan_amount,  $selected_loan,$interest,$totalPayable,$profile ,$mifos_profile);
if(empty($apply_loan))
{
  $this->displayText = "Thank you, Your loan application was not successful, contact support  ";
return;
}
if($apply_loan['success'] ==false)
{
  $this->displayText = "Thank you, Your loan application was not successful, contact support  ";
return;
}

if($apply_loan['success'] ==true)
{
  $this->displayText = "Thank you, You shall receive a loan confirmation shortly  ";
}

    break; 
case 2:
  $this->displayText = "You have cancelled your Loan Request, To Apply Loan, Select \n1. Kwamua Loan \n2. Help \n0. back";
  $this->nextFunction = "loanMenu";
  $this->sessionState = "CON";
  break;
}
}


function accountMenu()
{
  $mifos_profile = $this->getSessionVar("mifos_profile")??null;
  $this->displayText = "My Account, Select \n1. View Loan Balance \n2. View Repayment History \n0. Home";
  $this->nextFunction = "accountMenu";
  $this->sessionState = "CON";
  if($this->_input ==1)
  {
    $active_loans = $this->loanService->loansBalance($mifos_profile);

    Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}|active_loans".json_encode($active_loans));
    if($active_loans['success'] ==false)
    {
    
      $this->displayText = "Please try later to check your loan";
      $this->nextFunction = "loanPayment";
      $this->sessionState = "END";
      return;
    }
    if($active_loans['success'] ===201)
    {
      $this->displayText = "Your don't have any active loan at the moment";
      $this->nextFunction = "loanPayment";
      $this->sessionState = "END";
      return;
    }

    
    $totalPrincipal   = 0;
        $totalOutstanding = 0;
        $loanIds          = [];
   
        foreach ($active_loans['activeLoans'] as $loan) {
          $loanIds[] = $loan['loanId'];
          $totalPrincipal   += ceil($loan['principal']);
          $totalOutstanding += ceil($loan['outstanding']);
      }
       
    $this->saveSessionVar("active_loans",['loan_ids' =>$loanIds,"totalPrincipal"=>$totalPrincipal,"totalOutstanding"=>$totalOutstanding,"active_loans"=>$active_loans]);
    $this->displayText =  "CON Total Loan: KES {$totalPrincipal}\nOutstanding: KES {$totalOutstanding}\n1. Make Payment \n2. Make Partial Payment \n0 Home";
    $this->nextFunction = "loanPayment";
    $this->sessionState = "CON";
 
    return;
  }
  if($this->_input ==2)
  {
    $this->displayText = "You shall receive your loan repayment history shortly";
    $this->nextFunction = "accountMenu";
    $this->sessionState = "END";
    return;
  }
  if($this->_input ==0)
  {
    $this->displayText = " Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
    $this->nextFunction = "loanMenu";
    $this->previousPage = "";
    $this->sessionState = "CON";
    return;
  }
}

function loanPayment()
{
  $mifos_profile = $this->getSessionVar("mifos_profile")??null;
  $active_loans = $this->getSessionVar("active_loans")??null;
  if($this->_input ==1)
  {
    $this->displayText = "Please enter your mPesa PIN to clear the Loan";
    $this->nextFunction = "loanPayment";
    $this->sessionState = "END";
    \App\Jobs\ProcessPaymentRequest::dispatchSync(['active_loans'=>$active_loans,'mifos_profile'=>$mifos_profile,'mobile_number'=>$this->_msisdn,'amount'=>$active_loans['totalOutstanding']]);
$this->store_ussd(['mobile_number'=>$mifos_profile['mobileNo'],"menu"=>"PAY-LOAN","request"=>"Pay loan ".$mifos_profile['mobileNo']."  amount ". $active_loans['totalOutstanding'],"response"=>json_encode($active_loans),"request_data"=>['active_loans'=>$active_loans,'mifos_profile'=>$mifos_profile,'mobile_number'=>$this->_msisdn,'amount'=>$active_loans['totalOutstanding']],"request_response"=>[]]);
    return;
  }

  if($this->_input ==2)
  {
    $this->displayText = "Please enter your amount you wish to pay Maximum is KES ".$active_loans['totalOutstanding']." \n0. Back";
    $this->nextFunction = "loanPayment_partial";
    $this->sessionState = "CON";
    return;
  }

  if($this->_input ==0)
  {
    $this->displayText = " Select \n1. Apply Loan \n2. My Account\n3. Make Payment \n4. Help \n0. Exit";
    $this->nextFunction = "loanMenu";
    $this->previousPage = "";
    $this->sessionState = "CON";
    return;
  }

}

function loanPayment_partial()
{
//  $this->displayText = "Your active loan is KES 1,000 \n1. Make Payment \n2. Make Partial Payment \n0 Home";
  $this->nextFunction = "loanPayment";
  $this->sessionState = "CON";
  $mifos_profile = $this->getSessionVar("mifos_profile")??null;
  $active_loans = $this->getSessionVar("active_loans")??null;

  if($this->_input ==0)
  {
    $this->displayText = "Your active loan is KES ".$active_loans['totalOutstanding']." \n1. Make Payment \n2. Make Partial Payment \n0 Home";
    $this->nextFunction = "loanPayment";
    $this->sessionState = "CON";
    return;
  }

  if(!ctype_digit($this->_input))
{
  $this->displayText = "Invalid Entry \nPlease enter your amount you wish to pay Maximum is KES ".$active_loans['totalOutstanding']." \n0. Back";
  $this->nextFunction = "loanPayment_partial";
  $this->sessionState = "CON";
  return;
}
$this->store_ussd(['mobile_number'=>$mifos_profile['mobileNo'],"menu"=>"PAY-LOAN","request"=>"Pay loan ".$mifos_profile['mobileNo']."  amount ". $active_loans['totalOutstanding'],"response"=>json_encode($active_loans),"request_data"=>['active_loans'=>$active_loans,'mifos_profile'=>$mifos_profile,'mobile_number'=>$this->_msisdn,'amount'=>$active_loans['totalOutstanding']],"request_response"=>[]]);

//$pay_loan = $this->loanService->payLoan($active_loans,$mifos_profile,$this->_msisdn,$active_loans['totalOutstanding']);
\App\Jobs\ProcessPaymentRequest::dispatch(['active_loans'=>$active_loans,'mifos_profile'=>$mifos_profile,'mobile_number'=>$this->_msisdn,'amount'=>$active_loans['totalOutstanding']]);
$this->displayText = "Please enter your mPesa PIN to clear the Loan";
$this->nextFunction = "loanPayment";
$this->sessionState = "END";
return;

}
        }
?>
