<?php

namespace App\Console\Commands;

use App\Models\Academies;
use App\Models\Join;
use App\Models\Settlement;
use Illuminate\Console\Command;

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
            $sumPrice = Join::whereHas('training', function ($query) use ($academy) {
                $query->where('academy_id', $academy->id);
            })->sum('price');
            $oldSettlement = Settlement::where('partner_id', $academy->id)->latest()->first();

            if ($sumPrice > 0) {
                Settlement::create([
                    'partner_id' => $academy->id,
                    'total_amount' => $sumPrice,
                    'settlement_date' => now(),
                ]);

                $this->info('Settlement created for academy ID: ' . $academy->id . ' with amount: ' . $sumPrice);
            }
        }
    }
}
