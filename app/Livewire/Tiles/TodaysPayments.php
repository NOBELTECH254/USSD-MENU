<?php

namespace App\Livewire\Tiles;

use Livewire\Component;
use App\Models\Deposits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;



class TodaysPayments extends BaseComponent
{

    public function render()
    {

        $data = $this->handlePayments();
        return view('livewire.tiles.todays-payments', compact('data'));
    }

    private function handlePayments()
    {
        $payments = Deposits::whereDate('created_at', Carbon::today())
            ->get();

        $totalAmountPaid = $payments->count();

        $data = [
            'todays' => $totalAmountPaid,
        ];

        return $data;
    }

}
