<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/kategori', [App\Http\Controllers\Backend\CategoryController::class, 'index'])->name('category.index');
    Route::get('/kategori/tambah', [App\Http\Controllers\Backend\CategoryController::class, 'create'])->name('category.create');
    Route::get('/kategori/{id}/edit', [App\Http\Controllers\Backend\CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/kategori', [App\Http\Controllers\Backend\CategoryController::class, 'store'])->name('category.store');
    Route::delete('/kategori/{id}', [App\Http\Controllers\Backend\CategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('/barang', [App\Http\Controllers\Backend\ItemController::class, 'index'])->name('item.index');
    Route::get('/barang/tambah', [App\Http\Controllers\Backend\ItemController::class, 'create'])->name('item.create');
    Route::get('/barang/{id}/edit', [App\Http\Controllers\Backend\ItemController::class, 'edit'])->name('item.edit');
    Route::post('/barang', [App\Http\Controllers\Backend\ItemController::class, 'store'])->name('item.store');
    Route::delete('/barang/{id}', [App\Http\Controllers\Backend\ItemController::class, 'destroy'])->name('item.destroy');

    Route::get('/barang-masuk', [App\Http\Controllers\Backend\StockInController::class, 'index'])->name('stockIn.index');
    Route::get('/barang-masuk/tambah', [App\Http\Controllers\Backend\StockInController::class, 'create'])->name('stockIn.create');
    Route::get('/barang-masuk/{id}/edit', [App\Http\Controllers\Backend\StockInController::class, 'edit'])->name('stockIn.edit');
    Route::post('/barang-masuk', [App\Http\Controllers\Backend\StockInController::class, 'store'])->name('stockIn.store');
    Route::delete('/barang-masuk/{id}', [App\Http\Controllers\Backend\StockInController::class, 'destroy'])->name('stockIn.destroy');

    Route::get('/profile', [App\Http\Controllers\Backend\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/edit-avatar', [App\Http\Controllers\Backend\ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::delete('/profile/hapus-avatar', [App\Http\Controllers\Backend\ProfileController::class, 'deleteAvatar'])->name('profile.deleteAvatar');
    Route::post('/profile/ubah/password', [App\Http\Controllers\Backend\ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::post('/profile/ubah/biodata', [App\Http\Controllers\Backend\ProfileController::class, 'changeBiodata'])->name('profile.changeBiodata');
});

require __DIR__ . '/auth.php';
