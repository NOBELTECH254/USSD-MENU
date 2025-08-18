<?php

namespace App\Livewire\Tiles;

use Livewire\Component;
use App\Models\Payments\C2B;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ThisMonthsPayments extends BaseComponent
{

    public function render()
    {
        $data = $this->handlePayments();
        return view('livewire.tiles.this-months-payments', compact('data'));
    }

    private function handlePayments()
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $payments = C2B::where('clientID', auth()->user()->clientID)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();

        $totalAmountPaid = $payments->count();

        $data = [
            'month' => now()->format('F Y'),
            'thisMonthsPayments' => $totalAmountPaid,
        ];

        return $data;
    }


}