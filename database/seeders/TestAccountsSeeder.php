<?php

namespace Database\Seeders;

use App\Models\Academies;
use App\Models\AcademyCompetition;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentPayment;
use App\Models\AcademyStudentSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Partner / Academy Account
        $academy = Academies::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Arsenal Academy',
                'commercial_name' => 'أكاديمية آرسنال الرياضية',
                'owner_name' => 'مدير الأكاديمية',
                'phone' => '0501112233',
                'password' => '123456',
                'role' => 'manager',
                'business_type' => 'academy',
                'country_id' => 1,
                'city_id' => 1,
                'area_id' => 2,
                'status' => 1,
            ]
        );

        // 2. Player User Account
        $playerUser = User::updateOrCreate(
            ['phone' => '0501234567'],
            [
                'name' => 'أحمد اللاعب (Player)',
                'gender' => 'male',
                'birth_date' => '2010-05-15',
                'country_id' => 1,
                'city_id' => 1,
                'area_id' => 2,
            ]
        );

        // 3. Parent User Account
        $parentUser = User::updateOrCreate(
            ['phone' => '0507654321'],
            [
                'name' => 'محمد ولي الأمر (Parent)',
                'gender' => 'male',
                'country_id' => 1,
                'city_id' => 1,
                'area_id' => 2,
            ]
        );

        // 4. Academy Group
        $group = AcademyGroup::firstOrCreate(
            [
                'academy_id' => $academy->id,
                'name' => 'مجموعة الناشئين - فريق أ',
            ],
            [
                'capacity' => 20,
                'status' => 'active',
            ]
        );

        // 5. Academy Student
        $student = AcademyStudent::updateOrCreate(
            [
                'academy_id' => $academy->id,
                'phone' => '0501234567',
            ],
            [
                'user_id' => $playerUser->id,
                'name' => 'أحمد اللاعب',
                'guardian_name' => 'محمد ولي الأمر',
                'guardian_phone' => '0507654321',
                'status' => 'active',
            ]
        );

        // Attach student to group
        $student->groups()->syncWithoutDetaching([$group->id]);

        // 6. Student Subscription
        $subscription = AcademyStudentSubscription::updateOrCreate(
            [
                'academy_student_id' => $student->id,
                'academy_group_id' => $group->id,
            ],
            [
                'status' => 'active',
                'payment_status' => 'paid',
                'amount' => 500.00,
                'starts_on' => now()->startOfMonth()->toDateString(),
                'ends_on' => now()->addDays(14)->toDateString(),
            ]
        );

        // 7. Payment Record
        AcademyStudentPayment::updateOrCreate(
            [
                'academy_student_subscription_id' => $subscription->id,
            ],
            [
                'amount' => 500.00,
                'paid_at' => now()->toDateString(),
                'notes' => 'تم دفع الاشتراك بالكامل - نقداً',
            ]
        );

        // 8. Upcoming Match / Competition
        $competition = AcademyCompetition::updateOrCreate(
            [
                'academy_id' => $academy->id,
                'home_team_name' => 'أكاديمية آرسنال (فريق أ)',
                'opponent_name' => 'أكاديمية الهلال الرياضية',
            ],
            [
                'competition_date' => now()->addDays(4)->toDateString(),
                'starts_at' => '17:30',
                'venue' => 'ملعب الأكاديمية الرئيسي - مجمع الرياض',
                'status' => 'scheduled',
                'notes' => 'مباراة ودية ضمن دوري البراعم والناشئين',
            ]
        );

        // Attach student to upcoming competition match
        $competition->students()->syncWithoutDetaching([$student->id => ['role' => 'أساسي', 'notes' => 'مهاجم صريح']]);
    }
}
