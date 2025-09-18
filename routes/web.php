<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('products')->group(function () {
    Route::get('/', [PackageController::class, 'index']);
    Route::get('/{bill_of_transport}', [PackageController::class, 'packing_package'])
        ->name('packing.detail');
    Route::get('/detail/{package_grouping_number}', [PackageController::class, 'packing_detail'])
        ->name('packing.pdetail');
});

Route::prefix('item')->group(function(){
    Route::get('/',[ItemController::class ,'index']);
});



use App\Services\SalesReportService;
use Illuminate\Support\Facades\Storage;

