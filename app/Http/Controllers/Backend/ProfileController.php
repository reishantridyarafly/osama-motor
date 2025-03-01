<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        return view('backend.profile.index');
    }

    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:1000'
        ], [
            'avatar.required' => 'Silakan pilih gambar avatar terlebih dahulu!',
            'avatar.image' => 'File yang dipilih harus berupa gambar!',
            'avatar.mimes' => 'Gambar harus dalam format JPG, JPEG, PNG, atau GIF!',
            'avatar.max' => 'Ukuran gambar tidak boleh lebih dari 1MB!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            try {
                $user = Auth::user();
                if ($user->avatar !== 'avatar.png') {
                    Storage::delete('public/users-avatar/' . $user->avatar);
                }

                $path = $request->file('avatar')->store('users-avatar', 'public');
                $user->avatar = basename($path);
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Gambar profil berhasil disimpan!',
                    'data' => $user
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

    public function deleteAvatar()
    {
        try {
            $user = Auth::user();
            if ($user->avatar !== 'avatar.png') {
                Storage::delete('public/users-avatar/' . $user->avatar);
                $user->avatar = 'avatar.png';
                $user->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Gambar profil berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error occurred, please try againrred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validated = Validator::make(
            $request->all(),
            [
                'old_password' => 'required',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ],
            [
                'old_password.required' => 'Silakan isi password lama terlebih dahulu.',
                'password.required' => 'Silakan isi password baru terlebih dahulu.',
                'password.min' => 'Password harus terdiri dari minimal :min karakter.',
                'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
                'password_confirmation.required' => 'Silakan isi konfirmasi password terlebih dahulu.',
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            try {
                if (!Hash::check($request->old_password, Auth::user()->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Password lama salah!',
                        'error_password' => true
                    ]);
                } else {
                    User::whereId(Auth::user()->id)->update([
                        'password' => Hash::make($request->password)
                    ]);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Password berhasil di simpan!'
                    ], 200);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error occurred, please try againrred.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    public function changeBiodata(Request $request)
    {
        $id = Auth::user()->id;
        $validated = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string',
                'email' => 'required|string|unique:users,email,' . $id,
                'telephone' => 'required|min:11|max:15|unique:users,telephone,' . $id,
            ],
            [
                'first_name.required' => 'Silakan isi nama depan terlebih dahulu.',
                'first_name.string' => 'Nama depan harus berupa teks.',
                'email.required' => 'Silakan isi email terlebih dahulu.',
                'email.string' => 'Email harus berupa teks.',
                'email.unique' => 'Email telah digunakan.',
                'telephone.required' => 'Silakan isi no telepon terlbih dahulu.',
                'telephone.min' => 'Nomor telepon harus memiliki minimal :min karakter.',
                'telephone.max' => 'Nomor telepon tidak boleh memiliki lebih dari :max karakter.',
                'telephone.unique' => 'Nomor telepon telah digunakan.',
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            try {

                $user = User::findOrFail($id);

                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->telephone = $request->telephone;
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan!'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error occurred, please try againrred.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }
}
