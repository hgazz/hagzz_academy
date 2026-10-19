<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Repair labels only; collection entries, balances and timestamps remain intact.
        DB::table('academy_student_subscriptions')->orderBy('id')->chunkById(500, function ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                DB::transaction(function () use ($subscription) {
                    $row = DB::table('academy_student_subscriptions')->where('id', $subscription->id)->lockForUpdate()->first();
                    if (!$row) return;
                    $paid = (float) DB::table('academy_student_payments')->where('academy_student_subscription_id', $row->id)->sum('amount');
                    $net = max(0, (float) $row->amount - (float) $row->discount_amount);
                    $status = (int) round($paid * 100) >= (int) round($net * 100) ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
                    if ($row->payment_status !== $status) {
                        DB::table('academy_student_subscriptions')->where('id', $row->id)->update(['payment_status' => $status]);
                    }
                });
            }
        });
    }

    public function down(): void
    {
        // Incorrect historical labels cannot be reconstructed from the ledger.
    }
};
