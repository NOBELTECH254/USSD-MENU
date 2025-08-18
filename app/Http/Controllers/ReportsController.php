<?php

namespace App\Http\Controllers;
use DataTables;
use App\Http\Requests\StoreWithdrawsRequest;
use App\Http\Requests\UpdateWithdrawsRequest;
use App\Models\Withdraws;
use App\DataTables\WithdrawsDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportsController extends Controller
{


                public function game_report()
                {
                    if (\request()->ajax())
                   {

                        $start_date = request()->from_date ??Carbon::now()->startOfMonth();;
                        $end_date = request()->to_date ??Carbon::now();
        $data = DB::select(
          "select date(created_at)transaction_date, sum(bet_amount)bet_amount,count(*) total_bets,sum(amount_won)amount_won,count(distinct(if(amount_won>0,id,null)))won_count,(sum(bet_amount) -  sum(amount_won) )profit,concat(round(( sum(amount_won)/sum(bet_amount) )  *100,2),'%')rtp from game_sessions where   created_at >= ?  group by date(created_at)"
                      ,[$start_date]);
                    
                        return Datatables::of($data)
                                ->addIndexColumn()
                                ->filter(function ($instance) use ($data) {
                              })
                                ->make(true);


                            }

                    

                            
                    return view('pages/apps.reports.game-report');
                }

                
}
