<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Pastikan Carbon diimport

class StockOutController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $stockOut = StockOut::with('item')->orderBy('created_at', 'desc')->get();

      return DataTables::of($stockOut)
        ->addIndexColumn()
        ->addColumn('item', function ($data) {
          return $data->item->name ?? null;
        })
        ->addColumn('price_sale', function ($data) {
          return 'Rp ' . number_format($data->price_sale, 0, ',', '.');
        })
        ->addColumn('date', function ($data) {
          return \Carbon\Carbon::parse($data->created_at)->translatedFormat('l, d F Y H:i:s');
        })
        ->addColumn('total_price', function ($data) {
          $total_price = $data->quantity * $data->price_sale;
          return 'Rp ' . number_format($total_price, 0, ',', '.');
        })
        ->addColumn('action', function ($data) {
          return '
                        <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2" id="btnPrint" data-id="' . $data->id . '">
                            <i class="ti ti-printer fs-5"></i>Print
                        </button>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    $items = Item::whereHas('stockIns', function ($query) {
      $query->where('status', 'accepted');
    })->orderBy('name', 'asc')->get();

    return view('backend.stockOut.index', compact(['items']));
  }

  private function calculateSafetyStockAndROP($item_id)
  {
    $item = Item::find($item_id);
    if (!$item) {
      return ['safety_stock' => 0, 'reorder_point' => 0];
    }

    $stockIns = StockIn::where('item_id', $item->id)
      ->where('status', 'accepted')
      ->get();
    if ($stockIns->isEmpty()) {
      return ['safety_stock' => 0, 'reorder_point' => 0];
    }
    $stockOuts = StockOut::where('item_id', $item->id)->get();

    $totalQuantityOut = $stockOuts->sum('quantity');
    $totalDays = $stockOuts->count();
    $averageDailyDemand = $totalDays > 0 ? $totalQuantityOut / $totalDays : 0;

    $varianceDemand = $stockOuts->reduce(function ($carry, $stockOut) use ($averageDailyDemand) {
      return $carry + pow($stockOut->quantity - $averageDailyDemand, 2);
    }, 0);
    $stdDevDemand = $totalDays > 0 ? sqrt($varianceDemand / $totalDays) : 0;

    $totalLeadTimes = $stockIns->count(); // Menggunakan count sebagai proxy untuk lead time, perlu disesuaikan jika ada kolom lead time yang lebih spesifik
    $averageLeadTime = $totalLeadTimes > 0 ? $stockIns->count() / $totalLeadTimes : 0; // Ini mungkin tidak akurat, perlu disesuaikan dengan data lead time sebenarnya

    $varianceLeadTime = $stockIns->reduce(function ($carry, $stockIn) use ($averageLeadTime) {
      $date = Carbon::parse($stockIn->date);
      
      return $carry + pow($date->diffInDays($date) - $averageLeadTime, 2);
    }, 0);
    $stdDevLeadTime = $totalLeadTimes > 0 ? sqrt($varianceLeadTime / $totalLeadTimes) : 0;

    $z = 1.28; // Tingkat layanan untuk 90%

    $safetyStock = $z * sqrt(pow($stdDevDemand, 2) + pow($stdDevLeadTime, 2));
    $reorderPoint = $averageDailyDemand * $averageLeadTime + $safetyStock;

    return [
      'safety_stock' => ceil($safetyStock),
      'reorder_point' => ceil($reorderPoint)
    ];
  }


  public function getItemStock(Request $request)
  {
    $itemId = $request->input('item_id');

    if (!$itemId) {
      return response()->json(['error' => 'Item ID is required'], 400);
    }

    $item = Item::find($itemId);
    if (!$item) {
      return response()->json(['error' => 'Item not found'], 404);
    }

    $stockIns = StockIn::where('item_id', $itemId)
      ->where('quantity', '>', 0)
      ->where('status', 'accepted')
      ->orderBy('date', 'asc')
      ->get();

    $totalAvailable = $stockIns->sum('quantity');

    // Hitung safety stock untuk item ini
    $safetyStockData = $this->calculateSafetyStockAndROP($itemId);
    $safetyStock = $safetyStockData['safety_stock'];

    // Stok yang aman untuk dijual adalah total available dikurangi safety stock
    $sellableStock = max(0, $totalAvailable - $safetyStock);


    $latestStock = StockIn::where('item_id', $itemId)
      ->where('status', 'accepted')
      ->orderBy('created_at', 'desc')
      ->first();

    return response()->json([
      'available' => $totalAvailable, // Total stok yang ada
      'sellable_stock' => $sellableStock, // Stok yang bisa dijual (setelah dikurangi safety stock)
      'safety_stock_quantity' => $safetyStock, // Jumlah safety stock
      'item_id' => $itemId,
      'item_name' => $item->name,
      'price_sale' => $latestStock->price_sale ?? 0
    ]);
  }

  public function store(Request $request)
  {
    $validated = Validator::make($request->all(), [
      'items' => 'required|array|min:1',
      'items.*.id' => 'required|exists:items,id',
      'items.*.quantity' => 'required|integer|min:1',
      'items.*.price_sale' => 'required|numeric|min:0',
    ], [
      'items.required' => 'Data barang harus diisi',
      'items.array' => 'Format data barang tidak valid',
      'items.min' => 'Minimal 1 barang harus diisi',
      'items.*.id.required' => 'ID barang harus diisi',
      'items.*.id.exists' => 'Barang tidak ditemukan',
      'items.*.quantity.required' => 'Jumlah barang harus diisi',
      'items.*.quantity.integer' => 'Jumlah barang harus berupa angka',
      'items.*.quantity.min' => 'Jumlah barang minimal 1',
      'items.*.price_sale.required' => 'Harga jual harus diisi',
      'items.*.price_sale.numeric' => 'Harga jual harus berupa angka',
      'items.*.price_sale.min' => 'Harga jual tidak boleh negatif'
    ]);

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()], 422);
    }

    try {
      $results = [];
      $failedItems = [];
      $timestamp = now();

      foreach ($request->items as $item) {
        $itemId = $item['id'];
        $quantityToSell = $item['quantity'];
        $priceSale = $item['price_sale'];

        // Get stock ordered by both date and time (created_at)
        $stockIns = StockIn::where('item_id', $itemId)
          ->where('quantity', '>', 0)
          ->where('status', 'accepted') // Pastikan hanya stok yang diterima
          ->orderBy('created_at', 'asc')
          ->get();

        $totalAvailable = $stockIns->sum('quantity');

        // Hitung safety stock untuk item ini
        $safetyStockData = $this->calculateSafetyStockAndROP($itemId);
        $safetyStock = $safetyStockData['safety_stock'];

        // Stok yang aman untuk dijual adalah total available dikurangi safety stock
        $sellableStock = max(0, $totalAvailable - $safetyStock);

        if ($quantityToSell > $sellableStock) {
          $itemName = Item::find($itemId)->name ?? "ID: $itemId";
          $failedItems[] = [
            'item_name' => $itemName,
            'available' => $totalAvailable,
            'sellable_stock' => $sellableStock,
            'safety_stock_quantity' => $safetyStock,
            'requested' => $quantityToSell,
            'message' => "Stok yang tersedia untuk dijual hanya {$sellableStock} (Total: {$totalAvailable}, Safety Stock: {$safetyStock})."
          ];
          continue;
        }

        $price_buy = $stockIns->first()->price_buy ?? 0;
        $totalQuantityDeducted = 0;

        foreach ($stockIns as $stockIn) {
          if ($totalQuantityDeducted >= $quantityToSell) break;

          $remainingQuantityToDeduct = $quantityToSell - $totalQuantityDeducted;
          $quantityFromBatch = min($stockIn->quantity, $remainingQuantityToDeduct);

          $stockIn->quantity -= $quantityFromBatch;
          if ($stockIn->quantity == 0) {
            $stockIn->delete();
          } else {
            $stockIn->save();
          }
          $totalQuantityDeducted += $quantityFromBatch;
        }

        $stockOut = new StockOut();
        $stockOut->quantity = $quantityToSell;
        $stockOut->date = now();
        $stockOut->price_buy = $price_buy;
        $stockOut->price_sale = $priceSale;
        $stockOut->item_id = $itemId;
        $stockOut->cashier_id = auth()->user()->id;
        $stockOut->created_at = $timestamp;
        $stockOut->save();

        $results[] = $stockOut;
      }

      if (count($failedItems) > 0) {
        return response()->json([
          'status' => 'partial_success',
          'message' => 'Penjualan sebagian berhasil. Beberapa barang tidak dapat diproses karena stok aman tidak mencukupi.',
          'success_items' => $results,
          'failed_items' => $failedItems
        ], 207);
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Penjualan berhasil.',
        'timestamp' => $timestamp->toDateTimeString()
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan, silakan coba lagi.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function print($id)
  {
    $selected = StockOut::findOrFail($id);
    $time = $selected->created_at;

    $stockOuts = StockOut::with('item', 'cashier')
      ->whereBetween('created_at', [$time->copy()->subSeconds(5), $time->copy()->addSeconds(5)])
      ->orderBy('created_at')
      ->get();

    $pdf = Pdf::loadView('backend.stockOut.receipt', compact('stockOuts', 'time'))
      ->setPaper([0, 0, 226.77, 600 + count($stockOuts) * 20], 'portrait');

    return $pdf->download('struk_' . now()->format('YmdHis') . '.pdf');
  }
}
