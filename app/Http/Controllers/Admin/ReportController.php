<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Show all products report in HTML
    public function index(Request $request)
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return view('admin.reports.index', compact('products'));
    }

    // Download PDF report
    public function downloadPDF()
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('products'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('products_report_' . now()->format('Ymd_His') . '.pdf');
    }
}
