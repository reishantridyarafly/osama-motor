<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::where('supplier_id', auth()->user()->id)->orderBy('name', 'asc')->get();
            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('category', function ($data) {
                    return $data->category->name ?? null;
                })
                ->addColumn('price', function ($data) {
                    return 'Rp ' . number_format($data->price, 0, ',', '.');
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

        $categories = Category::orderBy('name', 'asc')->get();
        $suppliers = User::where('role', 'supplier')->orderBy('first_name', 'asc')->get();
        return view('backend.item.index', compact(['categories', 'suppliers']));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:items,name,' . $id,
                'category' => 'required',
                'stock' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
            ],
            [
                'name.required' => 'Silakan isi barang terlebih dahulu.',
                'name.unique' => 'Nama barang sudah tersedia.',
                'category.required' => 'Silakan pilih kategori terlebih dahulu.',
                'stock.required' => 'Silakan isi stok terlebih dahulu.',
                'stock.numeric' => 'Stok harus berupa angka.',
                'stock.min' => 'Stok tidak boleh kurang dari 0.',
                'price.required' => 'Silakan isi harga terlebih dahulu.',
                'price.numeric' => 'Harga harus berupa angka.',
                'price.min' => 'Harga tidak boleh kurang dari 0.',
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            try {
                Item::updateOrCreate([
                    'id' => $id
                ], [
                    'name' => $request->name,
                    'stock' => $request->stock,
                    'price' => $request->price,
                    'category_id' => $request->category,
                    'supplier_id' => auth()->user()->id,
                ]);

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
            $data = Item::find($id);
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
            $item = Item::find($request->id);

            if ($item) {
                $item->delete();

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

    public function getItemsBySupplier(Request $request)
    {
        $items = Item::where('supplier_id', $request->supplier_id)
            ->where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($items);
    }
}
