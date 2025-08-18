<?php

namespace App\Charts;

use Carbon\Carbon;
use Fidum\ChartTile\Charts\Chart;
use Fidum\ChartTile\Contracts\ChartFactory;
use App\Models\Deposits;
use Illuminate\Support\Facades\Log;

class TodaysPaymentsChart implements ChartFactory
{
    public static function make(array $settings): ChartFactory
    {
        return new static;
    }

    public function chart(): Chart
    {
        $chart = new Chart();

        // Fetch all payments for the specified client
        $payments = Deposits::get();

   

        // Calculate the sum of all amounts paid for the client
        $totalAmountPaid = $payments->sum('amountPaid');

       // Log::info($totalAmountPaid);

        // Create data array
        $data = [$totalAmountPaid];
        $data[] = 100 - $totalAmountPaid; // Assuming this is the remaining percentage

        $chart->labels(['Total Amount Paid = KSh'. ' ' . $totalAmountPaid, 'Remain---ing'])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => true,
                'animation' => [
                    'duration' => 0,
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'scales' => [
                    'xAxes' => ['display' => false],
                    'yAxes' => ['display' => false],
                ],
            ])
            ->dataset('Payments', 'pie', $data)
            ->label('Today\'s Payments')
            ->backgroundColor(['#FF9CEE', '#B28DFF']); // Adjust colors as needed

        return $chart;
    }
}