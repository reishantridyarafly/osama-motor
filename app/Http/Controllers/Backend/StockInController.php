<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StockInController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $stockIn = StockIn::with('item', 'supplier')->orderBy('created_at', 'desc')->get();
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
        ->addColumn('price_sale', function ($data) {
          return 'Rp ' . number_format($data->price_sale, 0, ',', '.');
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

    if ($id) {
      return $this->updateSingleItem($request);
    }

    return $this->storeMultipleItems($request);
  }

  private function updateSingleItem(Request $request)
  {
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
    }

    try {
      $stockIn = StockIn::find($request->id);
      $stockIn->update([
        'quantity' => $request->quantity,
        'item_id' => $request->item,
        'supplier_id' => $request->supplier,
        'price_sale' => $request->price_sale,
      ]);

      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil diperbarui.',
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat memperbarui data.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  private function storeMultipleItems(Request $request)
  {
    $basicValidation = Validator::make($request->all(), [
      'supplier' => 'required|exists:users,id',
      'items' => 'required|array|min:1',
    ], [
      'supplier.required' => 'Silakan pilih supplier terlebih dahulu.',
      'supplier.exists' => 'Supplier yang dipilih tidak valid.',
      'items.required' => 'Silakan tambahkan minimal satu barang.',
      'items.min' => 'Silakan tambahkan minimal satu barang.',
    ]);

    if ($basicValidation->fails()) {
      return response()->json(['errors' => $basicValidation->errors()]);
    }

    $itemRules = [];
    $itemMessages = [];

    foreach ($request->items as $index => $item) {
      $itemRules["items.{$index}.item_id"] = 'required|exists:items,id';
      $itemRules["items.{$index}.quantity"] = 'required|integer|min:1';
      $itemRules["items.{$index}.price_sale"] = 'required|numeric|min:0';

      $itemMessages["items.{$index}.item_id.required"] = "Silakan pilih barang untuk item " . ($index + 1) . ".";
      $itemMessages["items.{$index}.item_id.exists"] = "Barang yang dipilih pada item " . ($index + 1) . " tidak valid.";
      $itemMessages["items.{$index}.quantity.required"] = "Silakan isi quantity untuk item " . ($index + 1) . ".";
      $itemMessages["items.{$index}.quantity.integer"] = "Quantity item " . ($index + 1) . " harus berupa angka bulat.";
      $itemMessages["items.{$index}.quantity.min"] = "Quantity item " . ($index + 1) . " minimal 1.";
      $itemMessages["items.{$index}.price_sale.required"] = "Silakan isi harga jual untuk item " . ($index + 1) . ".";
      $itemMessages["items.{$index}.price_sale.numeric"] = "Harga jual item " . ($index + 1) . " harus berupa angka.";
      $itemMessages["items.{$index}.price_sale.min"] = "Harga jual item " . ($index + 1) . " minimal 0.";
    }

    $itemValidation = Validator::make($request->all(), $itemRules, $itemMessages);

    if ($itemValidation->fails()) {
      return response()->json(['errors' => $itemValidation->errors()]);
    }

    foreach ($request->items as $index => $item) {
      if (isset($item['item_id']) && isset($item['quantity'])) {
        $itemModel = Item::find($item['item_id']);
        if ($itemModel && $item['quantity'] > $itemModel->stock) {
          return response()->json([
            'errors' => [
              "items.{$index}.quantity" => ["Quantity item " . ($index + 1) . " melebihi stok tersedia (" . $itemModel->stock . ")."]
            ]
          ]);
        }
      }
    }

    try {
      DB::beginTransaction();

      foreach ($request->items as $item) {
        // Get item price
        $itemModel = Item::find($item['item_id']);

        StockIn::create([
          'quantity' => $item['quantity'],
          'date' => now(),
          'price_buy' => $itemModel->price,
          'price_sale' => $item['price_sale'],
          'item_id' => $item['item_id'],
          'supplier_id' => $request->supplier,
          'status' => 'request',
        ]);
      }

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil disimpan.',
      ], 201);
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat menyimpan data.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function edit($id)
  {
    try {
      $data = StockIn::find($id);

      if (!$data) {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan.',
        ], 404);
      }

      return response()->json($data);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat mengambil data.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function destroy(Request $request)
  {
    try {
      $stockIn = StockIn::find($request->id);

      if (!$stockIn) {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan.',
        ], 404);
      }

      $stockIn->delete();

      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil dihapus.',
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat menghapus data.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get items by supplier for AJAX requests
   */
  public function getItemsBySupplier(Request $request)
  {
    try {
      $supplierId = $request->supplier_id;

      if (!$supplierId) {
        return response()->json([]);
      }

      $items = Item::where('supplier_id', $supplierId)
        ->orWhereHas('suppliers', function ($query) use ($supplierId) {
          $query->where('supplier_id', $supplierId);
        })
        ->orderBy('name', 'asc')
        ->get(['id', 'name', 'price', 'stock']);

      return response()->json($items);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat mengambil data barang.',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
