<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemRequestController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $stockIn = StockIn::with('item', 'supplier')
        ->where('status', 'request')
        ->where('supplier_id', auth()->user()->id)
        ->orderBy('created_at', 'asc')
        ->get();
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
          if ($data->status == 'request') {
            return '<span class="badge rounded-pill text-bg-warning">Menunggu Persetujuan</span>';
          } elseif ($data->status == 'accepted') {
            return '<span class="badge rounded-pill text-bg-success">Disetujui</span>';
          } else {
            return '<span class="badge rounded-pill text-bg-danger">Ditolak</span>';
          }
        })
        ->addColumn('action', function ($data) {
          return '
          <div class="dropdown dropstart">
               <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="ti ti-dots-vertical fs-6"></i>
               </a>
               <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                 <li>
                   <button class="dropdown-item d-flex align-items-center gap-3" id="btnAccepted" data-id="' . $data->id . '">
                     <i class="fs-4 ti ti-check"></i>Terima
                   </button>
                 </li>
                 <li>
                   <button class="dropdown-item d-flex align-items-center gap-3" id="btnRejected" data-id="' . $data->id . '">
                     <i class="fs-4 ti ti-x"></i>Tolak
                   </button>
                 </li>
               </ul>
             </div>';
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    return view('backend.item_request.index');
  }

  public function history(Request $request)
  {
    if ($request->ajax()) {
      $stockIn = StockIn::with('item', 'supplier')
        ->where('supplier_id', auth()->user()->id)
        ->whereIn('status', ['accepted', 'rejected'])
        ->orderBy('created_at', 'asc')
        ->get();

      return DataTables::of($stockIn)
        ->addIndexColumn()
        ->addColumn('item', function ($data) {
          return $data->item->name ?? null;
        })
        ->addColumn('price', function ($data) {
          return 'Rp ' . number_format($data->item->price, 0, ',', '.');
        })
        ->addColumn('total_price', function ($data) {
          return 'Rp ' . number_format($data->item->price * $data->quantity, 0, ',', '.');
        })
        ->addColumn('date', function ($data) {
          return \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y');
        })
        ->addColumn('status', function ($data) {
          $badge = $data->status === 'accepted'
            ? '<span class="badge rounded-pill text-bg-success">Diterima</span>'
            : '<span class="badge rounded-pill text-bg-danger">Ditolak</span>';
          return $badge;
        })
        ->rawColumns(['status'])
        ->make(true);
    }

    return view('backend.item_request.history');
  }

  public function updateStatus(Request $request)
  {
    try {
      $stockIn = StockIn::find($request->id);
      if ($request->status == 'accepted') {
        $stockIn->item->stock -= $stockIn->quantity;
        $stockIn->item->save();
      }
      $stockIn->status = $request->status;
      $stockIn->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil diperbarui.',
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
