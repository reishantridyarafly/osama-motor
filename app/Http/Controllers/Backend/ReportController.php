<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StockOut;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }

    public function print(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!$start_date || !$end_date || $start_date > $end_date) {
            return redirect()->back()->with('error', 'Please select a valid date range');
        }

        $query = StockOut::with('item')
            ->orderBy('date', 'asc')
            ->whereBetween('date', [$start_date, $end_date]);

        $stockOuts = $query->get();

        if ($stockOuts->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the selected date range');
        }

        $period = Carbon::parse($start_date)->translatedFormat('d F Y') . ' - ' .
            Carbon::parse($end_date)->translatedFormat('d F Y');

        $total_quantity = $stockOuts->sum('quantity');

        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('report.print', [
                'stockOuts' => $stockOuts,
                'period' => $period,
                'total_quantity' => $total_quantity,
            ]);

            return $pdf->download('Laporan Transaksi - Periode ' . $period . '.pdf');
        }

        return view('report.print', [
            'stockOuts' => $stockOuts,
            'period' => $period,
            'total_quantity' => $total_quantity,
        ]);
    }
}
