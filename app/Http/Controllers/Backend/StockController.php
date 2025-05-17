<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockIn = StockIn::with('item', 'supplier')->where('status', 'accepted')->orderBy('created_at', 'asc')->get();
            return DataTables::of($stockIn)
                ->addIndexColumn()
                ->addColumn('item', function ($data) {
                    return $data->item->name ?? null;
                })
                ->addColumn('supplier', function ($data) {
                    return $data->supplier->first_name ?? null;
                })
                ->addColumn('price', function ($data) {
                    return 'Rp ' . number_format($data->price_buy, 0, ',', '.');
                })
                ->addColumn('price_sale', function ($data) {
                    return 'Rp ' . number_format($data->price_sale, 0, ',', '.');
                })
                ->addColumn('action', function ($data) {
                    return '
                    <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" id="btnEdit" data-id="' . $data->id . '">
                        <i class="fs-4 ti ti-edit"></i>Edit
                    </button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $suppliers = User::where('id', '!=', auth()->user()->id)
            ->where('role', 'supplier')
            ->orderBy('first_name', 'asc')
            ->get();
        return view('backend.stock.index', compact(['suppliers']));
    }

    public function edit($id)
    {
        try {
            $data = StockIn::with('supplier', 'item')->find($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditampilkan.',
                'id' => $data->id,
                'supplier' => $data->supplier->first_name,
                'item' => $data->item->name,
                'price' => $data->item->price,
                'stock' => $data->item->stock,
                'price_sale' => $data->price_sale,
                'quantity' => $data->quantity
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try again',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make($request->all(), [
            'price_sale' => 'required|numeric|min:0',
        ], [
            'price_sale.required' => 'Silakan isi harga jual terlebih dahulu.',
            'price_sale.numeric' => 'Harga jual harus berupa angka.',
            'price_sale.min' => 'Harga jual minimal 0.',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        }

        try {
            $stockIn = StockIn::findOrFail($id);
            $stockIn->update([
                'price_sale' => $request->price_sale,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try again',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
