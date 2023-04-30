<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('form');
// })->name('home');

Route::get('/', [CompanyController::class, 'index'])->name('home');

Route::post('/companies/historical-quotes', [CompanyController::class, 'handelSubmittedForm'])->name('companies.handelSubmittedForm');
