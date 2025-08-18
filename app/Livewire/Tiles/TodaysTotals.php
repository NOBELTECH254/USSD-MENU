<?php

namespace App\Livewire\Tiles;

use Livewire\Component;
use App\Models\Deposits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;



class TodaysTotals extends BaseComponent
{

    public function render()
    {

        $data = $this->handlePayments();
        return view('livewire.tiles.todays-totals', compact('data'));
    }

    private function handlePayments()
    {
        $payments = Deposits::whereDate('created_at', Carbon::today()) ->get();

        $totalAmountPaid = $payments->sum('amountPaid');

        $data = [
                'totalAmountPaid' => $totalAmountPaid,
        ];

        return $data;
    }

}