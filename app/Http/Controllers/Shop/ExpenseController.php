<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $search = $request->query('search');
        $category = $request->query('category');
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $expenses = Expense::forShop($shopId)
            ->when($search, function($query) use ($search) {
                return $query->where('description', 'like', "%{$search}%");
            })
            ->when($category, function($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($month, function($query) use ($month) {
                return $query->whereYear('expense_date', Carbon::parse($month)->year)
                            ->whereMonth('expense_date', Carbon::parse($month)->month);
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(15);

        $totalThisMonth = Expense::forShop($shopId)
            ->thisMonth()
            ->sum('amount');

        $categories = Expense::CATEGORIES;

        return view('shop.expenses.index', compact(
            'expenses', 
            'search', 
            'category', 
            'month', 
            'totalThisMonth',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categories = Expense::CATEGORIES;
        return view('shop.expenses.create', compact('categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'shop_id' => Auth::user()->shop_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('shop.expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        // Verify expense belongs to user's shop
        if ($expense->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        return view('shop.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        // Verify expense belongs to user's shop
        if ($expense->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $categories = Expense::CATEGORIES;
        return view('shop.expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Verify expense belongs to user's shop
        if ($expense->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $request->validate([
            'category' => 'required|string|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()->route('shop.expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        // Verify expense belongs to user's shop
        if ($expense->shop_id !== Auth::user()->shop_id) {
            abort(403, 'Unauthorized access to this expense.');
        }

        $expense->delete();

        return redirect()->route('shop.expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Get expense analytics for reports
     */
    public function analytics(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Total expenses for the month
        $totalExpenses = Expense::forShop($shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Expenses by category
        $expensesByCategory = Expense::forShop($shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('total_amount', 'desc')
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = Expense::forShop($shopId)
            ->selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        return view('shop.expenses.analytics', compact(
            'totalExpenses',
            'expensesByCategory',
            'monthlyTrend',
            'month'
        ));
    }
}