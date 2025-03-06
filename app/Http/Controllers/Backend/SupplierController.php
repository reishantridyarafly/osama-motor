<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
  public function index(Request $request)
  {
    if ($request->ajax()) {
      $suppliers = User::where('id', '!=', auth()->user()->id)
        ->where('role', 'supplier')
        ->orderBy('first_name', 'asc')
        ->get();
      return DataTables::of($suppliers)
        ->addIndexColumn()
        ->addColumn('status', function ($data) {
          $checked = $data->status == '0' ? 'checked' : '';
          return '<div class="form-check form-switch">
                      <input class="form-check-input status-toggle" type="checkbox" role="switch" data-id="' . $data->id . '" ' . $checked . '>
                      <label class="form-check-label" for="status">' . ($data->status == '0' ? 'Aktif' : 'Tidak Aktif') . '</label>
                  </div>';
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
        ->rawColumns(['action', 'status'])
        ->make(true);
    }
    return view('backend.supplier.index');
  }

  public function store(Request $request)
  {
    $id = $request->id;
    $validated = Validator::make(
      $request->all(),
      [
        'first_name' => 'required',
        'email' => 'required|unique:users,email,' . $id,
        'telephone' => 'required|min:11|max:13|unique:users,telephone,' . $id,
      ],
      [
        'first_name.required' => 'Silakan isi nama terlebih dahulu',
        'email.required' => 'Silakan isi email terlebih dahulu',
        'email.unique' => 'Email sudah digunakan',
        'telephone.required' => 'Silakan isi no telepon terlebih dahulu',
        'telephone.min' => 'No telepon :min karakter',
        'telephone.max' => 'No telepon :max karakter',
        'telephone.unique' => 'No telepon sudah digunakan',
      ]
    );

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()]);
    } else {
      try {
        $userData = [
          'first_name' => $request->first_name,
          'email' => $request->email,
          'telephone' => $request->telephone,
          'role' => 'supplier',
        ];

        if (!$id) {
          $userData['password'] = Hash::make('123456789');
        }

        User::updateOrCreate(['id' => $id], $userData);

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
      $data =  User::find($id);
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

  public function updateStatus(Request $request)
  {
    try {
      $produk = User::find($request->id);
      $produk->status = $request->status;
      $produk->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Status berhasil diperbarui.',
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
    $user = User::find($request->id);
    if ($user->avatar != 'avatar.png' && Storage::exists('public/users-avatar/' . $user->avatar)) {
      Storage::delete('public/users-avatar/' . $user->avatar);
    }
    $user->delete();
    return Response()->json(['user' => $user, 'message' => 'Data berhasil dihapus']);
  }
}
