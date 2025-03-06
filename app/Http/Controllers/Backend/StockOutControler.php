<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockOutControler extends Controller
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
        ->addColumn('unit_price', function ($data) {
          return 'Rp ' . number_format($data->unit_price, 0, ',', '.');
        })
        ->addColumn('date', function ($data) {
          return \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y');
        })
        ->make(true);
    }
    $items = Item::orderBy('name', 'asc')->get();
    return view('backend.stockOut.index', compact(['items']));
  }

  public function store(Request $request)
  {
    $validated = Validator::make($request->all(), [
      'item' => 'required|exists:items,id',
      'quantity' => 'required|integer|min:1',
    ], [

      'item.required' => 'Silakan pilih barang terlebih dahulu.',
      'item.exists' => 'Barang yang dipilih tidak valid.',
      'quantity.required' => 'Silakan isi quantity terlebih dahulu.',
      'quantity.integer' => 'Quantity harus berupa angka bulat.',
      'quantity.min' => 'Quantity minimal 1.',
    ]);

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()]);
    } else {
      try {
        $itemId = $request->input('item');
        $quantityToSell = $request->input('quantity');

        $stockIns = StockIn::where('item_id', $itemId)
          ->where('quantity', '>', 0)
          ->orderBy('date', 'asc')
          ->get();

        $totalAvailable = $stockIns->sum('quantity');
        if ($totalAvailable < $quantityToSell) {
          return response()->json([
            'error' => 'Stok tidak cukup untuk penjualan. Stok tersedia: ' . $totalAvailable,
            'available' => $totalAvailable,
            'requested' => $quantityToSell
          ], 400);
        }


        $totalQuantity = 0;
        $batches = [];
        $totalHpp = 0;

        foreach ($stockIns as $stockIn) {
          if ($totalQuantity >= $quantityToSell) {
            break;
          }

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

        $averageUnitCost = $totalHpp / $quantityToSell;

        $stockOut = new StockOut();
        $stockOut->item_id = $itemId;
        $stockOut->date = now();
        $stockOut->unit_price = $stockIns->first()->unit_cost;
        $stockOut->quantity = $quantityToSell;
        $stockOut->save();

        return response()->json([
          'status' => 'success',
          'message' => 'Penjualan berhasil.',
          'stock_out' => $stockOut,
          'total_hpp' => $totalHpp,
          'average_unit_cost' => $averageUnitCost
        ], 201);
      } catch (\Exception $e) {
        return response()->json([
          'status' => 'error',
          'message' => 'Error occurred, please try againrred',
          'error' => $e->getMessage()
        ], 500);
      }
    }
  }
}
