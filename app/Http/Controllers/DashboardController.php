<?php

namespace App\Http\Controllers;
use App\Models\AccountSignups;
use App\Models\UssdSessions;
use App\Models\MenuRequests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
    public function index()
    {
        addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);
     return view('pages/dashboards.index');
    }
    public function get_daily_dashboard(){

    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.
         $dashboard_tiles = [];
    // Retrieve the records for today
    $now= now()->format('Y-m-d');
    $ussd_requests = UssdSessions::query()->whereDate('created_at',  $now);
        $dashboard["ussd_today"] = $ussd_requests->count();
        $signup = AccountSignups::query()->whereDate('created_at',  $now);
        $dashboard["registrations_today"] = $signup->count();
        $loans = MenuRequests::query()->whereDate('created_at',  $now)->where("menu","like","LOAN-REQUESTS");
        // Monthly Stats
        $dashboard["loans_today"] = $loans->count();
        $payments = MenuRequests::query()->whereDate('created_at',  $now)->where("menu","like","PAY-LOAN");
        // Monthly Stats
        $dashboard["payments_today"] = $payments->count();

      array_walk_recursive($dashboard, function (&$val) {
        $val = number_format($val, 2);
    });


        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_week_dashboard(){
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.
         $dashboard_tiles = [];
  // Retrieve the records for week

  $ussd_requests = UssdSessions::query()->whereBetween('created_at', 
  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
);
  $dashboard["ussd_week"] = $ussd_requests->count();
  $signup = AccountSignups::query()->whereBetween('created_at', 
  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
);
  $dashboard["registrations_week"] = $signup->count();
  $loans = MenuRequests::query()->whereBetween('created_at', 
  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
)->where("menu","like","LOAN-REQUESTS");
  // Monthly Stats
  $dashboard["loans_week"] = $loans->count();
  $payments = MenuRequests::query()->whereBetween('created_at', 
  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
)->where("menu","like","PAY-LOAN");
  // Monthly Stats
  $dashboard["payments_week"] = $payments->count();


  
        array_walk_recursive($dashboard, function (&$val) {
            $val = number_format($val, 2);
        });
        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_month_dashboard(){
   
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.
         $dashboard_tiles = [];
  // Retrieve the records for month


  $ussd_requests = UssdSessions::query()->whereBetween('created_at', 
  [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
);
  $dashboard["ussd_month"] = $ussd_requests->count();
  $signup = AccountSignups::query()->whereBetween('created_at', 
  [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
);
  $dashboard["registrations_month"] = $signup->count();
  $loans = MenuRequests::query()->whereBetween('created_at', 
  [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
)->where("menu","like","LOAN-REQUESTS");
  // Monthly Stats
  $dashboard["loans_month"] = $loans->count();
  $payments = MenuRequests::query()->whereBetween('created_at', 
  [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
)->where("menu","like","PAY-LOAN");
  // Monthly Stats
  $dashboard["payments_month"] = $payments->count();



       
        array_walk_recursive($dashboard, function (&$val) {
            $val = number_format($val, 2);
        });
        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_chart(){
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.

$data =  UssdSessions::whereDate('created_at', '>=',Carbon::now()->startOfMonth())
->selectRaw(" date(created_at) day,count(*) requests")->groupBy(DB::raw('date(created_at)'))->get();
return response()->json(['success' => true, 'data' => $data]);

}

}
