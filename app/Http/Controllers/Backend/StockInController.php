<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockInController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $stockIn = StockIn::with('item')->orderBy('date', 'asc')->get();
      return DataTables::of($stockIn)
        ->addIndexColumn()
        ->addColumn('item', function ($data) {
          return $data->item->name ?? null;
        })
        ->addColumn('unit_cost', function ($data) {
          return 'Rp ' . number_format($data->unit_cost, 0, ',', '.');
        })
        ->addColumn('date', function ($data) {
          return \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y');
        })
        ->addColumn('action', function ($data) {
          return '
                  <div class="dropdown dropstart">
                      <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical fs-6"></i>
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                          <button class="dropdown-item d-flex align-items-center gap-3" id="btnEdit" data-id="' . $data->id . '">
                            <i class="fs-4 ti ti-edit"></i>Edit
                          </button>
                        </li>
                        <li>
                          <a class="dropdown-item d-flex align-items-center gap-3" id="btnDelete" data-id="' . $data->id . '">
                            <i class="fs-4 ti ti-trash"></i>Hapus
                          </a>
                        </li>
                      </ul>
                    </div>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    $items = Item::orderBy('name', 'asc')->get();
    return view('backend.stockIn.index', compact('items'));
  }

  public function store(Request $request)
  {
    $id = $request->id;
    $validated = Validator::make($request->all(), [
      'item' => 'required|exists:items,id',
      'quantity' => 'required|integer|min:1',
      'unit_cost' => 'required',
    ], [
      'item.required' => 'Silakan pilih barang terlebih dahulu.',
      'item.exists' => 'Barang yang dipilih tidak valid.',
      'quantity.required' => 'Silakan isi quantity terlebih dahulu.',
      'quantity.integer' => 'Quantity harus berupa angka bulat.',
      'quantity.min' => 'Quantity minimal 1.',
      'unit_cost.required' => 'Silakan isi harga satuan terlebih dahulu.',
    ]);

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()]);
    } else {
      try {
        if ($id) {
          $stockIn = StockIn::find($id);
          $stockIn->update([
            'quantity' => $request->quantity,
            'unit_cost' => str_replace(['Rp', ' ', '.'], '', $request->unit_cost),
            'item_id' => $request->item,
          ]);
        } else {
          StockIn::create([
            'quantity' => $request->quantity,
            'date' => now(),
            'unit_cost' => str_replace(['Rp', ' ', '.'], '', $request->unit_cost),
            'item_id' => $request->item,
          ]);
        }
        return response()->json([
          'status' => 'success',
          'message' => 'Data berhasil disimpan.',
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

  public function edit($id)
  {
    try {
      $data = StockIn::find($id);
      return response()->json($data);

      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil ditampilkan.',
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error occurred, please try againrred',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function destroy(Request $request)
  {
    try {
      $stockIn = StockIn::find($request->id);

      if ($stockIn) {
        $stockIn->delete();

        return response()->json([
          'status' => 'success',
          'message' => 'Data berhasil dihapus.',
        ], 201);
      }
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Error occurred, please try againrred',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
