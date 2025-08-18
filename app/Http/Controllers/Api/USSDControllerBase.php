<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use App\Models\UssdSessions;
use App\Services\ProfileService;
use App\Services\LoanService;

class USSDControllerBase extends Controller {
	public $_activityID;
	public $_msisdn;
	public $_accessPoint;
	public $sessionID;
	public $_input;
	public $_response; 
	/**
	 * navigation values
	 */
	public $previousPage;
	public $nextFunction;
	public $displayText;
	public $sessionState ="EXISTING";
	public $employeeProfile ;
	public $whitelist;
	protected $profileService;
	protected $loanService;

	public function __construct(Request $request,ProfileService $profileService,LoanService $loanService)
	{
		Log::info(__METHOD__."|".__LINE__."  | New Request |".json_encode($request->all()));
		$this->_msisdn =$request->phoneNumber;
		$this->_input = trim($request->text); 
		$inputArray = explode("*", $this->_input);
		$this->_input = $inputArray[sizeof($inputArray) - 1];
		
		$this->sessionID=$request->sessionId; // the ID is also not generated
		//load this session or create it since msisdn and sessionID are set.
		$this->loadSession($this->_msisdn, $this->sessionID);
		//get all navigation values from session
		$this->previousPage = $this->getSessionVar('previousPage');
		$this->nextFunction = $this->getSessionVar('nextFunction');
		$this->profileService = $profileService;
		$this->loanService = $loanService;

	}

	public function saveSession()
	{
$model = UssdSessions::insertOrIgnore(
[
'msisdn' => $this->_msisdn,
"session_id"=>$this->sessionID,
"state"=>"NEW",
]
);

	}


	/**
	 * To re-load a session or newly create one. In this case it uses the mobile
	 * number and the accessPoint
	 * @param string $msisdn
	 * @param string $sessionID
	 * @return void
	 */
	public function loadSession($msisdn, $sessionID) {
		//msisdn and sessionID concatenated will be the id of this session 
		$concatsessionname = $sessionID . ' ' . $msisdn;
		//load session / create
		$sessionID = md5($concatsessionname);
		session_id($sessionID);
		session_start();	
		$this->saveSession();	
		if (!isset($_SESSION['FIRST_DIAL_MINUTE'])) {
			$_SESSION['FIRST_DIAL_MINUTE'] = date('i');
			$this->sessionState ="NEW";
		}
		if (isset($_SESSION['FIRST_DIAL_MINUTE']) && (date('i') -
					$_SESSION['FIRST_DIAL_MINUTE'] >= 5)) {
			Log::info(__METHOD__."|".__LINE__."  |{$this->_msisdn}|{$this->sessionID}| Session timeout hence destroyed after "
					. ": 5 minutes");
			$this->destroySession();
		}
	}

	/**
	 * Force session destroy during timeout or
	 * SESSIONSTATE forced
	 */
	public function destroySession() {
		Log::info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}| Session state is END or timeout occured. Destroying this session");

		@session_unset();
		@session_destroy();
	}

	/**
	 * This function saves the session variable in the php session file
	 * @param type $sessionkey
	 * @param type $sessionvalue
	 * @return void
	 */
	public function saveSessionVar($sessionkey, $sessionvalue) {
		$_SESSION[$sessionkey] = $sessionvalue;
	}

	/**
	 * Returns a value from session
	 * @param string $sessionkey
	 * @return type
	 */
	public function getSessionVar($sessionkey = null) {
		try {
			$sessionvalue = isset($_SESSION["$sessionkey"]) ? $_SESSION["$sessionkey"] : "";
			return $sessionvalue;
		} catch (Exception $e) {
			Log::info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}| Experienced error while obtaining session value:" .$e);
			return null;

		}
	}

 public function processPOSTRequest($payload) {
        try {
      $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); //setting custom header
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //execute post
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //close connection
Log::info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}=======$this->url | MESSAGE => http response => $http_status|Request:".json_encode($payload) ."|response =>". json_encode($response, JSON_FORCE_OBJECT));
	curl_close($ch);
       return $response;
      } catch (Exception $e) {
	    Log::info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}=======$this->url | MESSAGE => http response => $http_status|Request:".json_encode($payload) ."|response Error".$e->getMessage());
return null;
      }
 }	
	


	/**
	 * Save navigation data and destroy session if necessary.
	 * @return string  response.
	 */
	public function finalizeProcessing() {
		//save navigation values within session first
		$this->saveSessionVar('nextFunction', $this->nextFunction);
		$this->saveSessionVar('previousPage', $this->previousPage);
		//we destory this session since end has been called.
		//$this->saveHop();
		if ($this->sessionState == "END") {
			$this->destroySession();
		}
		$response =  $this->sessionState." ".$this->displayText;
		Log::info(__METHOD__."|".__LINE__." |{$this->_msisdn}|{$this->sessionID}=======Finalized processing this request. Responding back to the USSD router with the following data===== $response");
		return $response;
	}


}