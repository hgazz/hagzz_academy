<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_students', function (Blueprint $table) {
            $table->string('country_code', 10)->nullable()->after('phone');
            $table->foreignId('country_id')->nullable()->after('country_code')->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained('areas')->nullOnDelete();
            $table->string('child_type', 30)->nullable()->after('birth_date');
            $table->string('school_name')->nullable()->after('child_type');
            $table->string('club_member', 10)->nullable()->after('school_name');
            $table->string('coach_preference', 30)->nullable()->after('club_member');
            $table->string('frequent_attendance', 30)->nullable()->after('coach_preference');
            $table->string('relation_with_child', 30)->nullable()->after('guardian_phone');
            $table->string('referral_source', 60)->nullable()->after('relation_with_child');
            $table->string('delivery_service', 10)->nullable()->after('referral_source');
            $table->string('medical_condition', 10)->nullable()->after('status');
            $table->date('start_date')->nullable()->after('medical_condition');
        });

        Schema::table('joins', function (Blueprint $table) {
            $table->foreignId('academy_student_id')->nullable()->after('user_id')
                ->constrained('academy_students')->nullOnDelete();
        });

        DB::table('joins')
            ->join('invoices', 'invoices.id', '=', 'joins.invoice_id')
            ->join('trainings', 'trainings.id', '=', 'joins.training_id')
            ->join('users', 'users.id', '=', 'joins.user_id')
            ->where('invoices.user_type', 'offline')
            ->select(['joins.id as join_id', 'trainings.academy_id', 'users.*'])
            ->orderBy('joins.id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $student = DB::table('academy_students')
                        ->where('academy_id', $row->academy_id)
                        ->where(function ($query) use ($row) {
                            $query->where('user_id', $row->id);
                            if ($row->phone) {
                                $query->orWhere('phone', $row->phone);
                            }
                        })->first();

                    $profile = [
                        'academy_id' => $row->academy_id, 'user_id' => $row->id, 'name' => $row->name,
                        'phone' => $row->phone, 'country_code' => $row->country_code, 'country_id' => $row->country_id,
                        'city_id' => $row->city_id, 'area_id' => $row->area_id, 'email' => $row->email,
                        'gender' => $row->gender, 'birth_date' => $row->birth_date, 'child_type' => $row->child_type,
                        'school_name' => $row->school_name, 'club_member' => $row->club_member,
                        'coach_preference' => $row->coach_preference, 'frequent_attendance' => $row->frequent_attendance,
                        'guardian_name' => $row->parent_name, 'guardian_phone' => $row->parent_phone,
                        'relation_with_child' => $row->relation_with_child, 'referral_source' => $row->referral_source,
                        'delivery_service' => $row->delivery_service, 'medical_condition' => $row->medical_condition,
                        'start_date' => $row->start_date, 'medical_notes' => $row->medical_condition_details,
                        'notes' => $row->additional_information, 'status' => 'active', 'updated_at' => now(),
                    ];

                    if ($student) {
                        $updates = [];
                        foreach ($profile as $key => $value) {
                            if ($value !== null && property_exists($student, $key) && blank($student->{$key})) {
                                $updates[$key] = $value;
                            }
                        }
                        if ($updates) DB::table('academy_students')->where('id', $student->id)->update($updates);
                        $studentId = $student->id;
                    } else {
                        $profile['created_at'] = now();
                        $studentId = DB::table('academy_students')->insertGetId($profile);
                    }

                    DB::table('joins')->where('id', $row->join_id)->update(['academy_student_id' => $studentId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('joins', fn (Blueprint $table) => $table->dropConstrainedForeignId('academy_student_id'));
        Schema::table('academy_students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('area_id');
            $table->dropColumn(['country_code', 'child_type', 'school_name', 'club_member', 'coach_preference',
                'frequent_attendance', 'relation_with_child', 'referral_source', 'delivery_service',
                'medical_condition', 'start_date']);
        });
    }
};
