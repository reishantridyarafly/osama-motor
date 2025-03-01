<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $category = Category::orderBy('name', 'asc')->get();
            return DataTables::of($category)
                ->addIndexColumn()
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
        return view('backend.category.index');
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:categories,name,' . $id,
            ],
            [
                'name.required' => 'Silakan isi kategori terlebih dahulu.',
                'name.unique' => 'Nama kategori sudah tersedia.',
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            try {
                Category::updateOrCreate([
                    'id' => $id
                ], [
                    'name' => $request->name,
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
            $data = Category::find($id);
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
            $category = Category::find($request->id);

            if ($category) {
                $category->delete();

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
