<?php

namespace App\Livewire\Tiles;

use Livewire\Component;
use App\Models\Payments\C2B;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ThisMonthsPaymentsTotals extends BaseComponent
{

    public function render()
    {
        $data = $this->handlePayments();
        return view('livewire.tiles.this-months-payments-totals', compact('data'));
    }

    private function handlePayments()
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $payments = C2B::where('clientID', auth()->user()->clientID)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();

        $totalAmountPaid = $payments->sum('amountPaid'); // Assuming 'amount' is the column representing the payment amount

        $data = [
            'month' => now()->format('F Y'),
            'thisMonthsTotalPayments' => $totalAmountPaid,
        ];

        return $data;
    }


}