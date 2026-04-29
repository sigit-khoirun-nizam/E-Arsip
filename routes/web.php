<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LetterTypeController;
use App\Http\Controllers\ArchiveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
})->name('home');

Route::resource('units', UnitController::class);
Route::resource('categories', CategoryController::class);
Route::resource('letter_types', LetterTypeController::class);
Route::resource('archives', ArchiveController::class);

Route::get('/admin/korins/{id}/print', [\App\Http\Controllers\KorinPrintController::class, 'printPdf'])
    ->name('korins.print')
    ->middleware(['web', 'auth']);

Route::get('/admin/ordners/{id}/print', [\App\Http\Controllers\OrdnerPrintController::class, 'printPdf'])
    ->name('ordners.print')
    ->middleware(['web', 'auth']);