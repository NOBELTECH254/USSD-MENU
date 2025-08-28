<?php
// app/Services/ClientProfileService.php
namespace App\Services;

use App\Models\Profiles;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\MessageTrait;
class ProfileService
{
    use MessageTrait;

    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = "https://lending.nobellending.co.ke/fineract-provider/api/v1";
        $this->username = "nobel";
        $this->password = "nobel3047";
    }
    public function checkProfile($mobile_number)
    {
             // Check if exists
             $profile = Profiles::where('mobile_number', $mobile_number)->first();
             if ($profile) {
                
  Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$mobile_number}|Profile =>".$profile->toJson());
  return ['success' => true, 'message' => 'Profile not found locally','profile'=>$profile];

             }
             return ['success' => false, 'message' => 'Profile not found locally'];
            }
            public function createProfile($profile,$mobile_number)
{
  // Create profil
      //  $pin = $this->generatePin();
      try {
        $pin = rand(1000, 9999);
        $customer_profile = Profiles::where('mobile_number', $mobile_number)->first();
        if ($customer_profile) {
            return ['success' => false, 'message' => 'Profile  found locally do not create' ];
        }
        $profile_added = Profiles::create([
            'customer_id' => $profile['accountNo'],
            'first_name' => $profile['firstname'],
            'last_name'  => $profile['lastname'],
            'display_name' =>$profile['displayName'],
            'mobile_number'    => $mobile_number,//$profile['mobileNo'],
            'hashed_pin'       => Hash::make($pin),
        ]);

        $message = message_template('account-activation', ['pin'=>$pin
        ]);
        $this->SendMessage($profile_added->id,$profile['mobileNo'] ,$message,"ACCOUNT-ACTIVATION");
/*   $menu_requests = MenuRequests::create([
                'mobile_number' => $mobile_number,
                'menu' => $menu,
                'request'  => $request,
                'response' =>$response,
                'request_data'    => $request_data,
                'request_response'       => $request_response,
            ]);
            */
        $this->store_ussd(['mobile_number'=>$profile['mobileNo'],"menu"=>"ACTIVATION","request"=>"Activate profile and send pin to customer ".$profile['mobileNo'],"response"=>"success","request_data"=>[],"request_response"=>[]]);
        return ['status' => 'success', 'message' => 'Profile created successfully', 'pin' => $pin,'profile' =>$profile];
    } catch (\Exception $e) {
        report($e);
        return ['success' => false, 'message' => $e->getMessage()];
    }
    }
    public function fetchFromMifos($mobile_number)
    {
	    $mobile_number = substr($mobile_number, -9);

            try {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withOptions([
                        'verify' => true, // Enforce SSL certificate verification
                    ])
                    ->get($this->baseUrl . '/clients', [
                        'sqlSearch' => "c.mobile_no like '%{$mobile_number}%'"
                    ]);

              
   Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |fetchFromMifos| {$mobile_number}|Response: {$response->body()} |Status :{$response->status()} ");

            if ($response->status() !== 200) {
                return ['success' => false, 'message' => 'API call failed'];
            }
    
            $data = $response->json();
    
            if (empty($data['totalFilteredRecords']) || $data['totalFilteredRecords'] < 1) {
                return ['success' => false, 'message' => 'No record found on Mifos'];
            }
$profile = $data['pageItems'][0]??null;

/* Validate mobile number
if (empty($profile['mobileNo']) || $profile['mobileNo'] !== $mobile_number) {
    return ['success' => false, 'message' => 'the returned mobile number is not valid please needs to be registered'];
}
*/
return ['success' => true, 'message' => 'profile matched to '.$mobile_number ,"profile"=>$profile];
    } catch (\Exception $e) {
        report($e);
        Log::channel('ussd_log')->info(__METHOD__."|".__LINE__." |{$mobile_number}|Error: {$e->getMessage()} ");


        return ['success' => false, 'message' => $e->getMessage()];
    }
    }

    public function reset_pin(string $uuid): array
    {

$profile = Profiles::where('uuid', $uuid)->firstOrFail();

        // Generate a strong random password
        $pin = rand(1000, 9999);

        $history = $profile->history ?? [];
        $history[] = [
            'action' => 'password_reset',
            'reason' => 'Admin reset via panel by '.auth()->user()->name ?? 'Guest', // Optional: can be dynamic
            'by'     => auth()->id(),
            'time'   => now()->toDateTimeString(),
        ];
        $profile->status_history = $history;


        // Update password
        $profile->hashed_pin = Hash::make($pin);
        $profile->save();

        // Dispatch queue job to send password
      //  SendUserPassword::dispatch($user, $newPassword);
      $message = message_template('pin-reset', [
        'name'     => $profile->display_name ?? "Customer",
        'pin' => $pin,
    ]);
 
    $this->SendMessage($profile->id,$profile->mobile_number ,$message,"PIN-RESET");

        return [
            'success' => true,
            'message' => 'Password reset successfully. The new password has been sent to the user.'.$message,
        ];
    }

    private function generatePin($length = 4)
    {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
