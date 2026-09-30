<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_01_000002_add_freeze_and_sessions_to_subscriptions_table
 *
 * يضيف للاشتراكات:
 *   - حالة "مجمد" (frozen) — مفيد للجيم والمراكز الصحية
 *   - تواريخ التجميد وسببه
 *   - رصيد الجلسات (للمراكز الصحية: x جلسة علاجية)
 *   - رقم عضوية مخزن (membership_number) لسرعة البحث
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── تعديل نوع عمود status لإضافة 'frozen' عبر SQL مباشر لتفادي قيود Doctrine DBAL ──
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE academy_student_subscriptions MODIFY COLUMN status ENUM('pending', 'active', 'frozen', 'expired', 'cancelled') DEFAULT 'active'"
        );

        Schema::table('academy_student_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('academy_student_subscriptions', 'frozen_from')) {
                $table->date('frozen_from')->nullable()->after('ends_on');
                $table->date('frozen_until')->nullable()->after('frozen_from');
                $table->date('original_ends_on')->nullable()->after('frozen_until')
                      ->comment('يحفظ تاريخ الانتهاء الأصلي قبل التمديد بسبب التجميد');
                $table->string('freeze_reason')->nullable()->after('original_ends_on');
                $table->string('frozen_by')->nullable()->after('freeze_reason');
            }

            if (!Schema::hasColumn('academy_student_subscriptions', 'sessions_total')) {
                $table->unsignedSmallInteger('sessions_total')->nullable()->after('amount')
                      ->comment('عدد الجلسات العلاجية الكلية في الباقة (null = بلا حد)');
                $table->unsignedSmallInteger('sessions_used')->default(0)->after('sessions_total')
                      ->comment('الجلسات المستخدمة حتى الآن');
            }

            if (!Schema::hasColumn('academy_student_subscriptions', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
                $table->string('discount_reason')->nullable()->after('discount_amount');
                $table->string('discount_approved_by')->nullable()->after('discount_reason');
                $table->timestamp('discount_approved_at')->nullable()->after('discount_approved_by');
            }
        });

        // ── جدول سجلات دخول البوابة (للجيم) ─────────────────────────────
        if (!Schema::hasTable('gym_gate_entries')) {
            Schema::create('gym_gate_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_student_id')->constrained('academy_students')->cascadeOnDelete();
                $table->foreignId('academy_subscription_id')
                      ->nullable()
                      ->constrained('academy_student_subscriptions')
                      ->nullOnDelete();
                $table->unsignedBigInteger('academy_id');
                $table->timestamp('entered_at');
                $table->timestamp('exited_at')->nullable();
                $table->enum('direction', ['in', 'out'])->default('in');
                $table->string('scan_method', 30)->default('qr')
                      ->comment('qr / barcode / manual / nfc');
                $table->string('station', 60)->nullable()
                      ->comment('اسم نقطة المسح: الباب الرئيسي، الاستقبال...');
                $table->boolean('subscription_valid')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['academy_id', 'entered_at']);
                $table->index(['academy_student_id', 'entered_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_gate_entries');

        Schema::table('academy_student_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'frozen_from', 'frozen_until', 'original_ends_on',
                'freeze_reason', 'frozen_by',
                'sessions_total', 'sessions_used',
            ]);
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])
                  ->default('active')
                  ->change();
        });
    }
};
