<?php

namespace App\Http\Controllers;
use App\Models\Withdraws;
use App\Models\Deposits;
use App\Models\GameSessions;
use App\Models\PlayerAccounts;

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
    $payments = Deposits::query()->whereDate('created_at',  $now);
        $dashboard["deposits_count_today"] = $payments->count();
        $dashboard["deposits_today"] = $payments->sum('amount');
        // Retrieve the records for the current month

        $withdraws = Deposits::query()->whereDate('created_at',  $now);
        $dashboard["withdraws_count_today"] = $withdraws->count();
        $dashboard["withdraws_today"] = $withdraws->sum('amount');
        $dashboard["balance_today"] =  $dashboard["deposits_today"] -$dashboard["withdraws_today"];


        $stakes = GameSessions::query()->whereDate('created_at',  $now);
        // Monthly Stats
        $dashboard["stakes_count_today"] = $stakes->count();
        $dashboard["stakes_today"] = $stakes->sum('bet_amount');

        $stakes_win = GameSessions::query()->whereDate('created_at',  $now)->where("ticket_outcome","=","ticket_win");
        // Monthly Stats
        $dashboard["stake_wins_count_today"] = $stakes_win->count();
        $dashboard["stake_wins_today"] = $stakes_win->sum('bet_amount');


        $stakes_lose = GameSessions::query()->whereDate('created_at',  $now)->where("ticket_outcome","=","ticket_lose");
        // Monthly Stats
        $dashboard["stake_loss_count_today"] = $stakes_lose->count();
        $dashboard["stake_loss_today"] = $stakes_lose->sum('bet_amount');


      array_walk_recursive($dashboard, function (&$val) {
        $val = number_format($val, 2);
    });


        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_week_dashboard(){
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.
         $dashboard_tiles = [];
  // Retrieve the records for week
        $payments = Deposits::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
);
        $dashboard["deposits_count_week"] = $payments->count();
        $dashboard["deposits_week"] = $payments->sum('amount');
        // Retrieve the records for the current month

        $withdraws = Deposits::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    );
        $dashboard["withdraws_count_week"] = $withdraws->count();
        $dashboard["withdraws_week"] = $withdraws->sum('amount');

        $stakes = GameSessions::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    );
        // Monthly Stats
        $dashboard["stakes_count_week"] = $stakes->count();
        $dashboard["stakes_week"] = $stakes->sum('bet_amount');
        $stakes_win = GameSessions::query()->whereBetween('created_at', 
        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    )->where("ticket_outcome","=","ticket_win");
        // Monthly Stats
        $dashboard["stake_wins_count_week"] = $stakes_win->count();
        $dashboard["stake_wins_week"] = $stakes_win->sum('bet_amount');

        $stakes_lose = GameSessions::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    )->where("ticket_outcome","=","ticket_lose");
        // Monthly Stats
        $dashboard["stake_loss_count_week"] = $stakes_lose->count();
        $dashboard["stake_loss_week"] = $stakes_lose->sum('bet_amount');

        $dashboard["balance_week"] =  $dashboard["deposits_week"] -$dashboard["withdraws_week"];
        array_walk_recursive($dashboard, function (&$val) {
            $val = number_format($val, 2);
        });
        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_month_dashboard(){
   
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.
         $dashboard_tiles = [];
  // Retrieve the records for month
        $payments = Deposits::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
    );
        $dashboard["deposits_count_month"] = $payments->count();
        $dashboard["deposits_month"] = $payments->sum('amount');
        // Retrieve the records for the current month

        $withdraws = Deposits::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
    );
        $dashboard["withdraws_count_month"] = $withdraws->count();
        $dashboard["withdraws_month"] = $withdraws->sum('amount');

        $stakes = GameSessions::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
    );
        // Monthly Stats
        $dashboard["stakes_count_month"] = $stakes->count();
        $dashboard["stakes_month"] = $stakes->sum('bet_amount');

        $stakes_win = GameSessions::query()->whereBetween('created_at', 
        [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
    )->where("ticket_outcome","=","ticket_win");
        // Monthly Stats
        $dashboard["stake_wins_count_month"] = $stakes_win->count();
        $dashboard["stake_wins_month"] = $stakes_win->sum('bet_amount');

        $stakes_lose = GameSessions::query() ->whereBetween('created_at', 
        [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]
    )->where("ticket_outcome","=","ticket_lose");
        // Monthly Stats
        $dashboard["stake_loss_count_month"] = $stakes_lose->count();
        $dashboard["stake_loss_month"] = $stakes_lose->sum('bet_amount');

        $dashboard["balance_month"] =  $dashboard["deposits_month"] -$dashboard["withdraws_month"];
        array_walk_recursive($dashboard, function (&$val) {
            $val = number_format($val, 2);
        });
        return response()->json(['success' => true,'dashboard' => $dashboard]);

}
public function get_chart(){
    \DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query where you have used group by. Note: make sure your query is correct.

$data =  GameSessions::whereDate('created_at', '>=',Carbon::now()->startOfMonth())
->selectRaw(" date(created_at) day,sum(bet_amount) as bet_amount,sum(if(ticket_outcome ='ticket_win',possible_win,0))wins")->groupBy(DB::raw('date(created_at)'))->get();
return response()->json(['success' => true, 'data' => $data]);

}

}
