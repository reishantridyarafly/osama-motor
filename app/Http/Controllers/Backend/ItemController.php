<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::orderBy('name', 'asc')->get();
            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('category', function ($data) {
                    return $data->category->name ?? null;
                })
                ->addColumn('total_stock', function ($data) {
                    $stock = StockIn::where('item_id', $data->id)->sum('quantity');
                    return $stock;
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
        return view('backend.item.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:items,name,' . $id,
                'category' => 'required',
            ],
            [
                'name.required' => 'Silakan isi barang terlebih dahulu.',
                'name.unique' => 'Nama barang sudah tersedia.',
                'category.required' => 'Silakan pilih kategori terlebih dahulu.',
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
                    'category_id' => $request->category,
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
}
