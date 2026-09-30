<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Admin\KotaController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Halaman Awal
|--------------------------------------------------------------------------
*/

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

Route::get('/api/news', [NewsController::class, 'index']);

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


    Route::prefix('admin')->name('admin.')->group(function () {
        // CWATP
        Route::get('/cwatp', [AdminController::class, 'index'])->name('cwatp');
        Route::post('/cwatp/export', [AdminController::class, 'exportAtp'])->name('cwatp.export');
        Route::post('/cwatp/export-bulk', [AdminController::class, 'exportBulkAtp'])->name('cwatp.export-bulk');

        // OPMC
        Route::get('/opmc', [AdminController::class, 'opmcIndex'])->name('opmc');
        Route::post('/opmc/export-bulk', [AdminController::class, 'exportBulkOpmc'])->name('opmc.export-bulk');

        // OPMC Reader
        Route::prefix('opmc-reader')->name('opmc.reader.')->group(function () {
            Route::get('/', [AdminController::class, 'opmcReaderIndex'])->name('index');
            Route::post('/process', [AdminController::class, 'opmcReaderProcess'])->name('process');
        });

        // --- FITUR BARU: OCR FOTO KE EXCEL ---
        Route::get('/opmc/ocr', [AdminController::class, 'opmcOcrIndex'])->name('opmc.ocr');
        Route::post('/opmc/process-ocr', [AdminController::class, 'processOcr'])->name('opmc.process-ocr');
        Route::post('/opmc/generate-excel', [AdminController::class, 'generateExcel'])->name('opmc.generate-excel');
    });
    /*
       |--------------------------------------------------------------------------
       | ADMIN
       |--------------------------------------------------------------------------
       */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->group(function () {

            // PERBAIKAN: Arahkan ke DashboardController alih-alih anonymous function
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::get('/laporan', function () {
                return view('admin.laporan');
            })->name('admin.laporan');

        });



    /*
    |--------------------------------------------------------------------------
    | employee
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:employee')
        ->prefix('employee')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('employee.dashboard');
            })->name('employee.dashboard');

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