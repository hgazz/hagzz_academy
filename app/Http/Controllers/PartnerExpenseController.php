<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PartnerActivityLog;
use App\Models\PartnerExpense;
use App\Models\PartnerExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('academy')->user();
        $academyId = $user->academy_id ?: $user->id;

        // Categories available (System + Partner Custom)
        $categories = PartnerExpenseCategory::whereNull('academy_id')
            ->orWhere('academy_id', $academyId)
            ->orderBy('is_system', 'desc')
            ->orderBy('name_ar')
            ->get();

        // Expenses Query
        $query = PartnerExpense::with(['category', 'creator'])
            ->where('academy_id', $academyId);

        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);

        // Financial Summary Calculations
        $totalExpenses = (float) (clone $query)->sum('amount');

        // Revenue query in matching period
        $revenueQuery = Invoice::whereHas('training', function ($q) use ($academyId) {
            $q->where('academy_id', $academyId);
        });

        if ($request->filled('from_date')) {
            $revenueQuery->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $revenueQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $totalRevenue = (float) $revenueQuery->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        return view('Academy.pages.expenses.index', compact(
            'expenses',
            'categories',
            'totalRevenue',
            'totalExpenses',
            'netProfit'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:partner_expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'period_type' => 'required|in:daily,monthly,quarterly,annual',
            'approved_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $user = auth('academy')->user();
        $academyId = $user->academy_id ?: $user->id;

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense = PartnerExpense::create([
            'academy_id' => $academyId,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'currency' => 'SAR',
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

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل المصروف بنجاح' : 'Expense recorded successfully');
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
