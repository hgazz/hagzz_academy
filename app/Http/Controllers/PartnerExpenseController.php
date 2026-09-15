<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coach;
use App\Models\Invoice;
use App\Models\PartnerActivityLog;
use App\Models\PartnerExpense;
use App\Models\PartnerExpenseCategory;
use App\Services\AcademyFinancialEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerExpenseController extends Controller
{
    public function index(Request $request, AcademyFinancialEngine $financialEngine)
    {
        $user = auth('academy')->user();
        $academy = ($user instanceof \App\Models\PartnerUser && $user->academy) ? $user->academy : $user;
        $academyId = $user->academy_id ?: $academy->id;
        $academyCurrencyCode = $academy->currency_code ?: 'SAR';
        $academyCurrencySymbol = $academy->currency_symbol ?: (app()->getLocale() === 'ar' ? 'ر.س' : 'SAR');

        // Coaches & Branches for this academy
        $coaches = Coach::where('academy_id', $academyId)->orderBy('name')->get();
        $branches = Address::where('academy_id', $academyId)->get();

        // Categories available (System + Partner Custom)
        $categories = PartnerExpenseCategory::whereNull('academy_id')
            ->orWhere('academy_id', $academyId)
            ->orderBy('is_system', 'desc')
            ->orderBy('name_ar')
            ->get();

        // Expenses Query
        $query = PartnerExpense::with(['category', 'creator', 'coach', 'branch'])
            ->where('academy_id', $academyId);

        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('coach_id')) {
            if ($request->coach_id === 'external') {
                $query->where('is_external_coach', true);
            } else {
                $query->where('coach_id', $request->coach_id);
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);

        // Unified financial summary using AcademyFinancialEngine
        $filterParams = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'branch_id' => $request->filled('branch_id') ? (int) $request->branch_id : null,
            'coach_id' => ($request->filled('coach_id') && $request->coach_id !== 'external') ? (int) $request->coach_id : null,
            'category_id' => $request->filled('category_id') ? (int) $request->category_id : null,
            'expense_type' => $request->expense_type,
        ];

        $profitSummary = $financialEngine->getNetProfitSummary($academyId, $filterParams);
        $totalRevenue = $profitSummary['total_revenue'];
        $totalExpenses = $profitSummary['total_expenses'];
        $netProfit = $profitSummary['net_profit'];
        $profitMargin = $profitSummary['profit_margin'];
        $revenueStreams = $profitSummary['revenue']['streams'];

        return view('Academy.pages.expenses.index', compact(
            'expenses',
            'categories',
            'coaches',
            'branches',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'profitMargin',
            'revenueStreams',
            'academyCurrencyCode',
            'academyCurrencySymbol'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:partner_expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'nullable|numeric|min:0.0001',
            'base_amount' => 'nullable|numeric|min:0.01',
            'expense_date' => 'required|date',
            'period_type' => 'required|in:daily,monthly,quarterly,annual',
            'approved_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'coach_id' => 'nullable|string',
            'branch_id' => 'nullable|exists:addresses,id',
            'expense_type' => 'nullable|string|max:40',
            'payment_method' => 'nullable|string|max:40',
        ]);

        $user = auth('academy')->user();
        $academy = ($user instanceof \App\Models\PartnerUser && $user->academy) ? $user->academy : $user;
        $academyId = $user->academy_id ?: $academy->id;
        $academyCurrencyCode = $academy->currency_code ?: 'SAR';

        $currency = strtoupper($request->currency);
        $amount = (float) $request->amount;
        $exchangeRate = (float) ($request->exchange_rate ?: 1.0);

        if ($currency === $academyCurrencyCode) {
            $exchangeRate = 1.0;
            $baseAmount = $amount;
        } else {
            $baseAmount = $request->filled('base_amount') ? (float) $request->base_amount : round($amount * $exchangeRate, 2);
            if ($amount > 0 && $baseAmount > 0 && !$request->filled('exchange_rate')) {
                $exchangeRate = round($baseAmount / $amount, 4);
            }
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $coachId = null;
        $isExternalCoach = false;
        if ($request->filled('coach_id')) {
            if ($request->coach_id === 'external') {
                $isExternalCoach = true;
            } elseif (is_numeric($request->coach_id)) {
                $coachId = (int) $request->coach_id;
            }
        }

        $expenseType = $request->expense_type ?: ($coachId || $isExternalCoach ? 'coach' : 'general');
        $paymentMethod = $request->payment_method ?: 'cash';

        $expense = PartnerExpense::create([
            'academy_id' => $academyId,
            'category_id' => $request->category_id,
            'coach_id' => $coachId,
            'is_external_coach' => $isExternalCoach,
            'branch_id' => $request->filled('branch_id') ? (int) $request->branch_id : null,
            'expense_type' => $expenseType,
            'payment_method' => $paymentMethod,
            'title' => $request->title,
            'amount' => $amount,
            'currency' => $currency,
            'exchange_rate' => $exchangeRate,
            'base_amount' => $baseAmount,
            'base_currency' => $academyCurrencyCode,
            'expense_date' => $request->expense_date,
            'period_type' => $request->period_type,
            'approved_by' => $request->approved_by ?: $user->name,
            'notes' => $request->notes,
            'receipt_image' => $receiptPath,
            'created_by_user_id' => $user->id,
        ]);

        PartnerActivityLog::log(
            'create_expense',
            "تم تسجيل مصروف جديد بقيمة {$expense->amount} ({$expense->title}) بواسطة {$user->name}"
        );

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل المصروف والقيد المحاسبي بنجاح' : 'Expense & journal entry recorded successfully');
    }


    public function destroy($id)
    {
        $user = auth('academy')->user();
        $academyId = $user->academy_id ?: $user->id;

        $expense = PartnerExpense::where('academy_id', $academyId)->findOrFail($id);
        $title = $expense->title;
        $expense->delete();

        PartnerActivityLog::log('delete_expense', "تم حذف المصروف ($title)");

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المصروف بنجاح' : 'Expense deleted successfully');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
        ]);

        $user = auth('academy')->user();
        $academyId = $user->academy_id ?: $user->id;

        PartnerExpenseCategory::create([
            'academy_id' => $academyId,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'icon' => $request->icon ?: 'fa-receipt',
            'is_system' => false,
        ]);

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم إضافة التصنيف الجديد بنجاح' : 'New expense category added successfully');
    }
}
