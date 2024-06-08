<?php

namespace App\Console\Commands;

use App\Models\Academies;
use App\Models\Join;
use App\Models\Settlement;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PartnerSettlements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settlements:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add a new command (PartnerSettlements)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $academies = Academies::get(['id', 'settlement_days_count']);

        foreach ($academies as $academy) {
            $oldSettlement = Settlement::where('partner_id', $academy->id)->latest()->first();

            // Determine the date to start fetching joins from
            if ($oldSettlement) {
                $startDate = Carbon::parse($oldSettlement->settlement_date)->addDays($academy->settlement_days_count);;
            } else {
                $startDate = Carbon::now()->subDays($academy->settlement_days_count);
            }

            // Calculate the sum of price and net amount for joins after the start date
            $sumPrice = Join::whereHas('training', function ($query) use ($academy, $startDate) {
                $query->where('academy_id', $academy->id)
                    ->where('created_at', '>', $startDate);
            })->sum('price');

            $netAmount = Join::whereHas('training', function ($query) use ($academy, $startDate) {
                $query->where('academy_id', $academy->id)
                    ->where('created_at', '>', $startDate);
            })->sum('net_amount');

            if ($sumPrice > 0) {
                Settlement::create([
                    'partner_id' => $academy->id,
                    'total_amount' => $sumPrice,
                    'net_amount' => $netAmount,
                    'settlement_date' => now(),
                ]);

                $this->info('Settlement created for academy ID: ' . $academy->id . ' with amount: ' . $sumPrice);
            }
        }
    }
}
