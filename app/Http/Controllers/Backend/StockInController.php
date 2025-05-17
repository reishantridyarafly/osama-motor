<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockInController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $stockIn = StockIn::with('item', 'supplier')->orderBy('created_at', 'asc')->get();
      return DataTables::of($stockIn)
        ->addIndexColumn()
        ->addColumn('item', function ($data) {
          return $data->item->name ?? null;
        })
        ->addColumn('supplier', function ($data) {
          return $data->supplier->first_name ?? null;
        })
        ->addColumn('price', function ($data) {
          return 'Rp ' . number_format($data->item->price, 0, ',', '.');
        })
        ->addColumn('date', function ($data) {
          return \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y');
        })
        ->addColumn('status', function ($data) {
          $badge = match ($data->status) {
            'request' => '<span class="badge rounded-pill text-bg-warning">Permintaan</span>',
            'accepted' => '<span class="badge rounded-pill text-bg-success">Diterima</span>',
            'rejected' => '<span class="badge rounded-pill text-bg-danger">Ditolak</span>',
            default => '<span class="badge rounded-pill text-bg-secondary">Unknown</span>'
          };
          return $badge;
        })
        ->addColumn('action', function ($data) {
          if (auth()->user()->role !== 'warehouse') {
            return '';
          }
          if ($data->status !== 'request') {
            return '';
          }
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
        ->rawColumns(['status', 'action'])
        ->make(true);
    }

    $suppliers = User::where('id', '!=', auth()->user()->id)
      ->where('role', 'supplier')
      ->orderBy('first_name', 'asc')
      ->get();
    return view('backend.stockIn.index', compact(['suppliers']));
  }

  public function store(Request $request)
  {
    $id = $request->id;
    $validated = Validator::make($request->all(), [
      'supplier' => 'required|exists:users,id',
      'item' => 'required|exists:items,id',
      'quantity' => 'required|integer|min:1',
      'price_sale' => 'required|numeric|min:0',
    ], [
      'supplier.required' => 'Silakan pilih supplier terlebih dahulu.',
      'supplier.exists' => 'Supplier yang dipilih tidak valid.',
      'item.required' => 'Silakan pilih barang terlebih dahulu.',
      'item.exists' => 'Barang yang dipilih tidak valid.',
      'quantity.required' => 'Silakan isi quantity terlebih dahulu.',
      'quantity.integer' => 'Quantity harus berupa angka bulat.',
      'quantity.min' => 'Quantity minimal 1.',
      'price_sale.required' => 'Silakan isi harga jual terlebih dahulu.',
      'price_sale.numeric' => 'Harga jual harus berupa angka.',
      'price_sale.min' => 'Harga jual minimal 0.',
    ]);

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()]);
    } else {
      try {
        if ($id) {
          $stockIn = StockIn::find($id);
          $stockIn->update([
            'quantity' => $request->quantity,
            'item_id' => $request->item,
            'supplier_id' => $request->supplier,
            'price_sale' => $request->price_sale,
          ]);
        } else {
          StockIn::create([
            'quantity' => $request->quantity,
            'date' => now(),
            'price_buy' => $request->price,
            'price_sale' => $request->price_sale,
            'item_id' => $request->item,
            'supplier_id' => $request->supplier,
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
