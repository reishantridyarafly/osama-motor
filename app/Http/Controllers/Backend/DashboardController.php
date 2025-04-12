<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $items = Item::all();

        $safetyStockData = [];

        foreach ($items as $item) {
            // Mengambil data stock in dan stock out untuk item tertentu
            $stockIns = StockIn::where('item_id', $item->id)
                ->where('status', 'accepted')
                ->get();
            $stockOuts = StockOut::where('item_id', $item->id)->get();

            // Menghitung permintaan harian rata-rata (D)
            $totalQuantityOut = $stockOuts->sum('quantity');
            $totalDays = $stockOuts->count();
            $averageDailyDemand = $totalDays > 0 ? $totalQuantityOut / $totalDays : 0;

            // Menghitung deviasi standar permintaan (σD)
            $varianceDemand = $stockOuts->reduce(function ($carry, $stockOut) use ($averageDailyDemand) {
                return $carry + pow($stockOut->quantity - $averageDailyDemand, 2);
            }, 0);
            $stdDevDemand = $totalDays > 0 ? sqrt($varianceDemand / $totalDays) : 0;

            // Menghitung waktu pengiriman rata-rata (L)
            $totalLeadTimes = $stockIns->count() > 0 ? $stockIns->count() : 1;
            $averageLeadTime = $totalLeadTimes > 0 ? $stockIns->count() / $totalLeadTimes : 0;

            // Menghitung deviasi standar waktu pengiriman (σL)
            $varianceLeadTime = $stockIns->reduce(function ($carry, $stockIn) use ($averageLeadTime) {
                $date = Carbon::parse($stockIn->date);
                return $carry + pow($date->diffInDays($date) - $averageLeadTime, 2);
            }, 0);
            $stdDevLeadTime = $totalLeadTimes > 0 ? sqrt($varianceLeadTime / $totalLeadTimes) : 0;

            // Tingkat layanan (Z) untuk 90% (1.28)
            $z = 1.28;

            // Menghitung safety stock (SS)
            $safetyStock = $z * sqrt(pow($stdDevDemand, 2) + pow($stdDevLeadTime, 2));

            // Menghitung reorder point (ROP)
            $reorderPoint = $averageDailyDemand * $averageLeadTime + $safetyStock;
            $currentStock = StockIn::where('item_id', $item->id)
                ->where('status', 'accepted')
                ->where('quantity', '>', 0)
                ->sum('quantity');

            // Menyimpan hasil perhitungan
            $safetyStockData[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'current_stock' => $currentStock,
                'average_daily_demand' => round($averageDailyDemand, 2),
                'safety_stock' => ceil($safetyStock),
                'reorder_point' => ceil($reorderPoint),
                'stock_status' => $currentStock <= $safetyStock ? 'danger' : ($currentStock <= $reorderPoint ? 'warning' : 'safe')
            ];
        }

        return view('backend.dashboard.index', compact('safetyStockData'));
    }
}
