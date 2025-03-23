<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/profile', [App\Http\Controllers\Backend\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/edit-avatar', [App\Http\Controllers\Backend\ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::delete('/profile/hapus-avatar', [App\Http\Controllers\Backend\ProfileController::class, 'deleteAvatar'])->name('profile.deleteAvatar');
    Route::post('/profile/ubah/password', [App\Http\Controllers\Backend\ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::post('/profile/ubah/biodata', [App\Http\Controllers\Backend\ProfileController::class, 'changeBiodata'])->name('profile.changeBiodata');
});

Route::middleware(['auth', 'role:warehouse,owner'])->group(function () {
    Route::get('/barang-masuk', [App\Http\Controllers\Backend\StockInController::class, 'index'])->name('stockIn.index');

    Route::get('/barang-keluar', [App\Http\Controllers\Backend\StockOutControler::class, 'index'])->name('stockOut.index');
});

Route::middleware(['auth', 'role:warehouse'])->group(function () {
    Route::get('/barang-masuk/tambah', [App\Http\Controllers\Backend\StockInController::class, 'create'])->name('stockIn.create');
    Route::get('/barang-masuk/{id}/edit', [App\Http\Controllers\Backend\StockInController::class, 'edit'])->name('stockIn.edit');
    Route::post('/barang-masuk', [App\Http\Controllers\Backend\StockInController::class, 'store'])->name('stockIn.store');
    Route::delete('/barang-masuk/{id}', [App\Http\Controllers\Backend\StockInController::class, 'destroy'])->name('stockIn.destroy');

    Route::get('/barang-keluar/tambah', [App\Http\Controllers\Backend\StockOutControler::class, 'create'])->name('stockOut.create');
    Route::get('/barang-keluar/{id}/edit', [App\Http\Controllers\Backend\StockOutControler::class, 'edit'])->name('stockOut.edit');
    Route::post('/barang-keluar', [App\Http\Controllers\Backend\StockOutControler::class, 'store'])->name('stockOut.store');
    Route::delete('/barang-keluar/{id}', [App\Http\Controllers\Backend\StockOutControler::class, 'destroy'])->name('stockOut.destroy');

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
});

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/supplier', [App\Http\Controllers\Backend\SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/tambah', [App\Http\Controllers\Backend\SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/supplier', [App\Http\Controllers\Backend\SupplierController::class, 'store'])->name('supplier.store');
    Route::post('/supplier/updateStatus', [App\Http\Controllers\Backend\SupplierController::class, 'updateStatus'])->name('supplier.updateStatus');
    Route::get('/supplier/{id}/edit', [App\Http\Controllers\Backend\SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('/supplier/{id}', [App\Http\Controllers\Backend\SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{id}', [App\Http\Controllers\Backend\SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/laporan', [App\Http\Controllers\Backend\ReportController::class, 'index'])->name('report.index');
    Route::post('/laporan/print', [App\Http\Controllers\Backend\ReportController::class, 'print'])->name('report.print');
});

require __DIR__ . '/auth.php';
