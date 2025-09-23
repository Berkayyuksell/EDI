<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('products')->group(function () {
    Route::get('/', [PackageController::class, 'index'])
    ->name('packing.index');
       Route::get('/all',[PackageController::class,'comingAllProduct'])
        ->name('packing.allproduct');
    Route::get('/{bill_of_transport}', [PackageController::class, 'packing_package'])
        ->name('packing.detail');
    Route::get('/detail/{package_grouping_number}', [PackageController::class, 'packing_detail'])
        ->name('packing.pdetail');
    Route::post('/approve/{id}', [PackageController::class, 'approve'])
        ->name('packing.approve');
    Route::post('/report',[PackageController::class,'reportItem'])
        ->name('package.report');
    Route::delete('/report/{id}',[PackageController::class,'deleteReportItem'])
        ->name('reports.delete');

});

Route::prefix('item')->group(function(){
    Route::get('/',[ItemController::class ,'index']);
});


    Route::get('/reports', [PackageController::class, 'GoodsManList'])
        ->name('package.reports');


use App\Services\SalesReportService;
use Illuminate\Support\Facades\Storage;

