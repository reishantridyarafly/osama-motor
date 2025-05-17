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

    $latestStock = StockIn::where('item_id', $itemId)
      ->where('status', 'accepted')
      ->orderBy('created_at', 'desc')
      ->first();

    return response()->json([
      'available' => $totalAvailable,
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
          ->orderBy('created_at', 'asc')
          ->get();

        $price_buy = $stockIns->first()->price_buy;
        $totalAvailable = $stockIns->sum('quantity');

        if ($totalAvailable < $quantityToSell) {
          $itemName = Item::find($itemId)->name ?? "ID: $itemId";
          $failedItems[] = [
            'item_name' => $itemName,
            'available' => $totalAvailable,
            'requested' => $quantityToSell
          ];
          continue;
        }

        $totalQuantity = 0;
        $batches = [];
        $totalHpp = 0;

        foreach ($stockIns as $stockIn) {
          if ($totalQuantity >= $quantityToSell) break;

          $remainingQuantity = $quantityToSell - $totalQuantity;
          $quantityFromBatch = min($stockIn->quantity, $remainingQuantity);

          $batches[] = [
            'stock_in_id' => $stockIn->id,
            'quantity' => $quantityFromBatch,
            'unit_cost' => $stockIn->unit_cost,
          ];

          $totalQuantity += $quantityFromBatch;
          $totalHpp += $quantityFromBatch * $stockIn->unit_cost;
        }

        foreach ($batches as $batch) {
          $stockIn = StockIn::find($batch['stock_in_id']);
          $stockIn->quantity -= $batch['quantity'];
          if ($stockIn->quantity == 0) {
            $stockIn->delete();
          } else {
            $stockIn->save();
          }
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
          'message' => 'Beberapa barang tidak dapat diproses karena stok tidak mencukupi.',
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
        'message' => 'Error occurred, please try again',
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
