<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/login', App\Livewire\Auth\Login::class)->middleware('guest')->name('login');
Route::get('/logout', App\Http\Controllers\LogoutController::class)->middleware('auth')->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', App\Livewire\Home::class)->name('home');

    Route::prefix('work-order')->name('work-order.')->group(function () {
        Route::get('/', App\Livewire\WorkOrder\Index::class)->name('index');
        Route::get('/create', App\Livewire\WorkOrder\Create::class)->name('create')->middleware('ensureProductionManager');
    });

    Route::prefix('reports')->name('reports.')->middleware(['ensureProductionManager'])->group(function () {
        Route::get('/work-order-recap', App\Livewire\Reports\WorkOrderRecap::class)->name('work-order-recap');
        Route::get('/operator-recap', App\Livewire\Reports\OperatorRecap::class)->name('operator-recap');
    });
});