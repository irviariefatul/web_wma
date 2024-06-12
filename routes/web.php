<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PeramalanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*Route::get('/', function () {
    return view('auth.login');
});*/

Auth::routes();

Route::get('/', [DashboardController::class, 'index']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Profil
Route::get('/profil', [ProfilController::class, 'index'])->name('profil');

// Admin
Route::resource('/users', UserController::class);

// Forecasting
Route::resource('/peramalans', PeramalanController::class);
Route::post('/test-peramalan', [PeramalanController::class, 'scrapRequest'])->name('test-peramalan');
Route::post('/peramalans/import', [PeramalanController::class, "import"])->name('import');


// PDF
// Route::get('/peramalans/{peramalan}/report/pdf', [PeramalanController::class, 'generatePDF']);
Route::get('/report-pdf', [PeramalanController::class, 'generatePDF'])->name('report-pdf');

// Peramalan untuk 1 hari kedepan
Route::get('/hitung-nilai-peramalan', [PeramalanController::class, 'hitungNilaiPeramalan']);
Route::get('/hitung-nilai-peramalan2', [DashboardController::class, 'hitungNilaiPeramalan2']);

// Total MAPE
Route::get('/hitung-total-mape', [PeramalanController::class, 'hitungTotalMAPE']);

Route::get("/test", [PeramalanController::class, "scrap"])->name('scraping');

// Download Template
Route::get('/download-forecast-template', [PeramalanController::class, 'downloadTemplate']);

// Reset
Route::get('/reset-data', [PeramalanController::class, 'resetData'])->name('reset-data');
