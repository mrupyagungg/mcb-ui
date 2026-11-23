<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Admin\KotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\HRDController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\AssetsController;

/*
|--------------------------------------------------------------------------
| Halaman Awal
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
// Route::get('/employee/login', [EmployeeController::class, 'showLoginForm'])->name('employee.login');
Route::get('/employee/attendance', [DashboardController::class, 'index'])->name('employee.attendance');

// Opsi 1: Arahkan otomatis ke halaman Login
// Route::get('/', function () {
//     return redirect()->route('login');
// })->name('home');

Route::get('/', function () {
    return view('mcb-ui.landing'); // Sesuaikan dengan nama file aslinya (misal: index.blade.php -> 'mcb-ui.index')
})->name('home');

// Rute Menu Navigasi Lainnya
Route::get('/about', function () {
    return view('mcb-ui.about'); // Pastikan file resources/views/mcb-ui/about.blade.php ada
});

Route::get('/service', function () {
    return view('mcb-ui.service');
});

Route::get('/team', function () {
    return view('mcb-ui.team');
});

Route::get('/portfolio', function () {
    return view('mcb-ui.portfolio');
});

Route::get('/contact', function () {
    return view('mcb-ui.contact');
});
Route::get('/blog', function () {
    return view('mcb-ui.blog');
});

// Route::get('/api/news', [NewsController::class, 'index']);

Route::get('/single', function () {
    return view('mcb-ui.single');
});


/*
|--------------------------------------------------------------------------
| Semua User Login
|--------------------------------------------------------------------------
*/
// CONTOH BENAR (Menggunakan Controller):
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Daftar & Manajemen Template
    Route::get('/templates', [MasterDataController::class, 'listTemplates'])->name('template.index');
    Route::get('/templates/download/{filename}', [MasterDataController::class, 'downloadTemplate'])->name('template.download');

    // Route yang sudah ada sebelumnya...
    Route::get('/template-upload', [MasterDataController::class, 'uploadTemplateForm'])->name('template.upload');
    Route::post('/template-upload', [MasterDataController::class, 'uploadTemplate'])->name('template.store');
    Route::post('/export-bulk-atp', [MasterDataController::class, 'exportBulkAtp'])->name('export.bulk.atp');
});

Route::middleware(['auth'])->group(function () {


    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // ================= CWATP =================
        Route::get('/cwatp', [AdminController::class, 'index'])->name('cwatp');

        // TAMBAHKAN 2 BARIS INI:
        Route::get('/cwatp/checklist-pole', [AdminController::class, 'checklistPole'])->name('cwatp.checklist-pole');
        Route::post('/cwatp/export-pole', [AdminController::class, 'exportPole'])->name('cwatp.export-pole');

        Route::post('/cwatp/export', [AdminController::class, 'exportAtp'])->name('cwatp.export');
        Route::post('/cwatp/export-bulk', [AdminController::class, 'exportBulkAtp'])->name('cwatp.export-bulk');

        // ================= OPMC =================
        Route::get('/opmc', [AdminController::class, 'opmcIndex'])->name('opmc');
        Route::post('/opmc/export-bulk', [AdminController::class, 'exportBulkOpmc'])->name('opmc.export-bulk');

        // ================= OPMC Reader =================
        Route::prefix('opmc-reader')->name('opmc.reader.')->group(function () {
            Route::get('/', [AdminController::class, 'opmcReaderIndex'])->name('index');
            Route::post('/process', [AdminController::class, 'opmcReaderProcess'])->name('process');

            Route::post('/generate-otdr-pdf', [AdminController::class, 'generateOtdrPdf'])
                ->name('generate-otdr-pdf');
        });

        // ================= FITUR: OCR FOTO KE EXCEL =================
        Route::get('/opmc/ocr', [AdminController::class, 'opmcOcrIndex'])->name('opmc.ocr');
        Route::post('/opmc/process-ocr', [AdminController::class, 'processOcr'])->name('opmc.process-ocr');
        Route::post('/opmc/generate-excel', [AdminController::class, 'generateExcel'])->name('opmc.generate-excel');
    });

    /*
    |--------------------------------------------------------------------------
    | employee
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:employee')
        ->prefix('employee')
        ->group(function () {

            Route::get('/dashboard', [AbsensiController::class, 'dashboard'])
                ->name('employee.dashboard');

            Route::get('/attendance', [AbsensiController::class, 'index'])
                ->name('employee.attendance');

            Route::get('/riwayat', [AbsensiController::class, 'riwayat'])
                ->name('employee.history');
        });

    Route::middleware('auth')->group(function () {

        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])
            ->name('absensi.masuk');

        Route::post('/absensi/keluar', [AbsensiController::class, 'keluar'])
            ->name('absensi.keluar');
    });



    /*
    |--------------------------------------------------------------------------
    | HRD
    |--------------------------------------------------------------------------
    */

    Route::prefix('hrd')->name('hrd.')->middleware(['auth', 'role:hrd'])->group(function () {

        Route::get('/dashboard', [HRDController::class, 'dashboard'])->name('dashboard');
        Route::get('/karyawan', [HRDController::class, 'karyawan'])->name('karyawan');
        Route::post('/karyawan', [HRDController::class, 'storeKaryawan'])
            ->name('karyawan.store');
        Route::get('/riwayat-absensi', [HRDController::class, 'riwayatAbsensi'])->name('riwayat-absensi');
        Route::get('/assets', [HRDController::class, 'assets'])->name('assets');

        // IMPORT EXPORT KARYAWAN
        Route::post('/karyawan/import', [HRDController::class, 'importKaryawan'])
            ->name('karyawan.import');

        Route::get('/karyawan/export', [HRDController::class, 'exportKaryawan'])
            ->name('karyawan.export');

        Route::get('/karyawan/template', [HRDController::class, 'templateKaryawan'])
            ->name('karyawan.template');

        // KPI
        Route::prefix('kpi')->name('kpi.')->group(function () {
            Route::get('/dashboard', [KPIController::class, 'dashboard'])->name('dashboard');
            Route::get('/penilaian', [KPIController::class, 'penilaian'])->name('penilaian');
            Route::get('/rekapitulasi', [KPIController::class, 'rekapitulasi'])->name('rekapitulasi');

            // PENGATURAN INDIKATOR
            Route::get('/indikator', [KPIController::class, 'indikator'])->name('indikator');
            Route::post('/indikator', [KPIController::class, 'storeIndikator'])->name('indikator.store');
            Route::put('/indikator/{id}', [KPIController::class, 'updateIndikator'])->name('indikator.update');
            Route::delete('/indikator/{id}', [KPIController::class, 'destroyIndikator'])->name('indikator.destroy');
        });
        Route::get('/laporan', [HRDController::class, 'laporan'])->name('laporan');
    });


    /*
    |--------------------------------------------------------------------------
    | ASSET MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::prefix('assets')->name('assets.')->middleware(['auth'])->group(function () {

        // Dashboard Assets
        Route::get('/dashboard', [AssetsController::class, 'dashboard'])
            ->name('dashboard');

        // Data Assets
        Route::get('/data', [AssetsController::class, 'index'])
            ->name('index');

        Route::get('/create', [AssetsController::class, 'create'])
            ->name('create');

        Route::post('/store', [AssetsController::class, 'store'])
            ->name('store');

        Route::get('/{asset}/edit', [AssetsController::class, 'edit'])
            ->name('edit');

        Route::put('/{asset}', [AssetsController::class, 'update'])
            ->name('update');

        Route::delete('/{asset}', [AssetsController::class, 'destroy'])
            ->name('destroy');

        // Categories
        Route::get('/categories', [AssetsController::class, 'categories'])
            ->name('categories');

        // Assignment Assets
        Route::get('/assignment', [AssetsController::class, 'assignment'])
            ->name('assignment');

        Route::post('/assignment', [AssetsController::class, 'assign'])
            ->name('assign');

        // Return Assets
        Route::get('/return', [AssetsController::class, 'returns'])
            ->name('return');

        Route::post('/return', [AssetsController::class, 'processReturn'])
            ->name('process-return');

        // Maintenance
        Route::get('/maintenance', [AssetsController::class, 'maintenance'])
            ->name('maintenance');

        Route::post('/maintenance', [AssetsController::class, 'storeMaintenance'])
            ->name('maintenance.store');

        // Asset History
        Route::get('/history', [AssetsController::class, 'history'])
            ->name('history');

        // Reports
        Route::get('/reports', [AssetsController::class, 'reports'])
            ->name('reports');

    });


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');


    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');


    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


});


require __DIR__ . '/auth.php';