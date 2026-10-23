<?php
namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class DashboardFinancialSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        DB::purge('sqlite');
        $tables = [
            'countries'=>['iso2','currency_code'], 'academies'=>['country_id'],
            'academy_students'=>['academy_id','country_id'],
            'academy_student_subscriptions'=>['academy_student_id'],
            'academy_student_payments'=>['academy_student_subscription_id','method','amount'],
            'trainings'=>['academy_id'], 'invoices'=>['training_id'],
            'partner_expense_categories'=>['academy_id','name_ar','name_en'],
            'partner_expenses'=>['academy_id','category_id','amount','base_amount'],
        ];
        foreach($tables as $name=>$columns) Schema::create($name,function(Blueprint $t)use($columns){
            $t->id(); foreach($columns as $column) $t->string($column)->nullable();
        });
        app()->setLocale('ar');
    }

    public function test_expense_categories_sum_base_currency_and_exclude_other_academies(): void
    {
        DB::table('partner_expense_categories')->insert([
            ['id'=>1,'academy_id'=>null,'name_ar'=>'تشغيل','name_en'=>'Operations'],
            ['id'=>2,'academy_id'=>7,'name_ar'=>'مواقع','name_en'=>'Venues'],
            ['id'=>3,'academy_id'=>8,'name_ar'=>'غيرنا','name_en'=>'Other'],
        ]);
        DB::table('partner_expenses')->insert([
            ['academy_id'=>7,'category_id'=>1,'amount'=>100,'base_amount'=>120],
            ['academy_id'=>7,'category_id'=>2,'amount'=>50,'base_amount'=>null],
            ['academy_id'=>8,'category_id'=>1,'amount'=>900,'base_amount'=>900],
            ['academy_id'=>8,'category_id'=>3,'amount'=>800,'base_amount'=>800],
        ]);
        $method=new \ReflectionMethod(DashboardController::class,'expenseCategoriesBreakdown');
        $result=$method->invoke(app(DashboardController::class),7);
        $this->assertEquals(['تشغيل'=>120,'مواقع'=>50],$result->pluck('total','name')->all());
        $this->assertEquals(170,$result->sum('total'));
    }

    public function test_qatar_payment_distribution_includes_bank_transfers_and_reconciles(): void
    {
        DB::table('countries')->insert(['id'=>1,'iso2'=>'QA','currency_code'=>'QAR']);
        DB::table('academies')->insert([['id'=>7,'country_id'=>1],['id'=>8,'country_id'=>1]]);
        DB::table('academy_students')->insert([['id'=>11,'academy_id'=>7,'country_id'=>1],['id'=>12,'academy_id'=>8,'country_id'=>1]]);
        DB::table('academy_student_subscriptions')->insert([['id'=>21,'academy_student_id'=>11],['id'=>22,'academy_student_id'=>12]]);
        foreach([['bank_transfer',300,21],['card',200,21],['cash',100,21],['bank_transfer',999,22]] as [$method,$amount,$sub]) {
            DB::table('academy_student_payments')->insert(['academy_student_subscription_id'=>$sub,'method'=>$method,'amount'=>$amount]);
        }
        $method=new \ReflectionMethod(DashboardController::class,'getPaymentMethodBreakdown');
        $result=$method->invoke(app(DashboardController::class),7);
        $qa=collect($result['countries']['QA']['methods']);
        $this->assertEquals(300,$qa->firstWhere('key','bank_transfer')['amount']);
        $this->assertSame(1,$qa->firstWhere('key','bank_transfer')['count']);
        $this->assertEquals(600,$qa->sum('amount'));
    }
}
