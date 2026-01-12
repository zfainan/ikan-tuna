<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CuttingController;
use App\Http\Controllers\CuttingLController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceLController;
use App\Http\Controllers\KategoriByprodukCtController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KategoriBeratPenerimaanController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeLController;
use App\Http\Controllers\GradeSController;
use App\Http\Controllers\GradeHController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\PackingReportController;
use App\Http\Controllers\PenerimaanIkanController;
use App\Http\Controllers\PenerimaanIkanReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\SupplierController;
use App\Models\Cutting;
use App\Models\KategoriProduk;
use App\Models\PenerimaanIkan;
use Illuminate\Support\Facades\Route;

//POST
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

//GET
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::get('/reload-captcha', [LoginController::class, 'reloadCaptcha']);
Route::get('/penerimaan-ikan-pdf', [PenerimaanIkanController::class, 'penerimaanIkanPdf'])->name('penerimaan-ikan.pdf');
Route::get('/cutting-pdf', [CuttingController::class, 'cuttingPdf'])->name('cutting.pdf');
Route::get('/kategori-byproduk-ct-pdf', [KategoriByprodukCtController::class, 'kategoriByprodukCtPdf'])->name('kategori-byproduk-ct.pdf');
Route::get('/grading', \App\Livewire\GradingProses::class)->name('grading.index')->middleware('auth');
Route::get('/cuttingl', \App\Livewire\CuttingByL::class)->name('cuttingl.index')->middleware('auth');

//PUT
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');

//RESOURCE
Route::resource('suppliers', SupplierController::class)->middleware('auth');
Route::resource('penerimaan_ikan', PenerimaanIkanController::class)->middleware('auth');
Route::resource('grade', GradeController::class)
    ->parameters(['grade' => 'grade:grade_id'])
    ->middleware('auth');
Route::resource('grade_hservice', GradeHController::class)
    ->parameters(['grade_hservice' => 'grade_hservice_id'])
    ->middleware('auth');
Route::resource('cutting', CuttingController::class)->middleware('auth');
Route::resource('cuttingl', CuttingLController::class)->middleware('auth');
Route::resource('servicel', ServiceLController::class)->middleware('auth');
Route::resource('service', ServiceController::class)->middleware('auth');
Route::resource('kategori-byproduk-ct', KategoriByprodukCtController::class)
    ->parameters(['kategori-byproduk-ct' => 'kategori_byproduk_id'])
    ->middleware('auth');
Route::resource('kategori-produk', KategoriProdukController::class)
    ->parameters(['kategori-produk' => 'kategori_produk_id'])
    ->middleware('auth');

Route::resource('penerimaan_ikan', PenerimaanIkanController::class);
Route::resource('packings', PackingController::class)->only(['index']);
Route::resource('stock', StockController::class)->only(['index']);

//MIDDLEWARE ADMIN
Route::middleware('is_admin')->group(function () {
    Route::get('/admin', DashboardController::class);

    //Resource admin
    Route::resource('akun', AccountController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('grade', GradeController::class);
    Route::resource('gradel', GradeLController::class);
    Route::resource('grade_service', GradeSController::class);
    Route::resource('grade_hservice', GradeHController::class);
    Route::resource('kategori_berat_penerimaan', KategoriBeratPenerimaanController::class)
        ->parameters(['kategori_berat_penerimaan' => 'kategori_berat_id']);
    Route::resource('kategori-byproduk-ct', KategoriByprodukCtController::class)
        ->parameters(['kategori-byproduk-ct' => 'kategori_byproduk_id']);
    Route::resource('suppliers', SupplierController::class);

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/penerimaan-ikan', PenerimaanIkanReportController::class)->name('penerimaan_ikan.index');
        Route::post('/penerimaan-ikan', [PenerimaanIkanReportController::class, 'print'])
            ->name('penerimaan_ikan.print');

        Route::get('/stock', StockReportController::class)->name('stock.index');
        Route::post('/stock', [StockReportController::class, 'print'])
            ->name('stock.print');

        Route::get('/packing', PackingReportController::class)->name('packing.index');
        Route::post('/packing', [PackingReportController::class, 'print'])
            ->name('packing.print');
    });
});

//MIDDLEWARE KARYAWAN
Route::middleware('is_karyawan')->group(function () {
    Route::get('/karyawan', DashboardController::class);
});

//GET LAPORAN
Route::get('/laporan_ikan_masuk', function () {
    return view('admin.laporan_penerimaan_ikan');
})->middleware('is_admin');

Route::get('/laporan_cutting', function () {
    return view('admin.laporan.laporan_cutting');
})->middleware('is_admin');

Route::get('/laporan_service', function () {
    return view('admin.laporan.laporan_service');
})->middleware('is_admin');

Route::get('/laporan_packing', function () {
    return view('admin.laporan.laporan_packing');
})->middleware('is_admin');

Route::get('/laporan_stok_masuk', function () {
    return view('admin.laporan_stok_masuk');
})->middleware('is_admin');

Route::get('/laporan_stok_keluar', function () {
    return view('admin.laporan_stok_keluar');
})->middleware('is_admin');

Route::get('/get-grade/{ikan}', function (KategoriProduk $ikan) {
    return response()->json(['grade' => $ikan->grade]);
});

Route::get('/get-supplier/{penerimaan_ikan}', function (PenerimaanIkan $penerimaan_ikan) {
    return response()->json(['nama_supplier' => $penerimaan_ikan->supplier->nama_supplier]);
});

Route::get('/get-supplier-by-batch/{no_batch}', function ($no_batch) {
    $cutting = Cutting::where('no_batch', $no_batch)->first();
    if ($cutting && $cutting->penerimaan_ikan) {
        $supplier_id = $cutting->penerimaan_ikan->supplier->supplier_id;
        return response()->json(['supplier_id' => $supplier_id]);
    }
    return response()->json(['supplier_id' => ''], 404);
});
