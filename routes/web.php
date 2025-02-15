<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::group([
    'as' => 'registry.',
    'prefix' => 'registry',
    'middleware' => ['auth'],
], function () {

    // Ministry Routes
    Route::match(['get', 'post'], 'ministries/datatables', [\App\Http\Controllers\National\Eregistry\MinistryController::class, 'getDataTables'])->name('ministries.datatables');
    Route::resource('ministries', \App\Http\Controllers\National\Eregistry\MinistryController::class);
    

    // Division Routes
    Route::match(['get', 'post'], 'divisions/datatables', [\App\Http\Controllers\National\Eregistry\DivisionController::class, 'getDataTables'])->name('divisions.datatables');
    Route::resource('divisions', \App\Http\Controllers\National\Eregistry\DivisionController::class);
    

    // Folder Routes
    Route::match(['get', 'post'], 'folders/datatables', [\App\Http\Controllers\National\Eregistry\FolderController::class, 'getDataTables'])->name('folders.datatables');
    Route::resource('folders', \App\Http\Controllers\National\Eregistry\FolderController::class);
    

    // File Routes
   
    // Route for serving the actual file content
    Route::get('files/{id}/view', [\App\Http\Controllers\National\Eregistry\FileController::class, 'viewFile'])->name('files.view');
    Route::get('files/{id}/download', [\App\Http\Controllers\National\Eregistry\FileController::class, 'download'])
     ->name('files.download');

    Route::match(['get', 'post'], 'files/datatables', [\App\Http\Controllers\National\Eregistry\FileController::class, 'getDataTables'])->name('files.datatables');
    Route::resource('files', \App\Http\Controllers\National\Eregistry\FileController::class);
   

    // File Type Routes
    Route::get('/registry/file-types/{fileTypeId}/dynamic-form', [
        'as' => 'file-types.dynamic-form',
        'uses' => 'App\Http\Controllers\National\Eregistry\FileTypeController@dynamicForm'
    ]);
    Route::match(['get', 'post'], 'file-types/datatables', [\App\Http\Controllers\National\Eregistry\FileTypeController::class, 'getDataTables'])->name('file-types.datatables');
    Route::resource('file-types', \App\Http\Controllers\National\Eregistry\FileTypeController::class);
   

    // File Access Routes
    Route::match(['get', 'post'], 'file-access/datatables', [\App\Http\Controllers\National\Eregistry\FileAccessController::class, 'getDataTables'])->name('file-access.datatables');
    Route::resource('file-access', \App\Http\Controllers\National\Eregistry\FileAccessController::class);
    

    // Movement Routes
    Route::match(['get', 'post'], 'movements/datatables', [\App\Http\Controllers\National\Eregistry\MovementController::class, 'getDataTables'])->name('movements.datatables');
    Route::resource('movements', \App\Http\Controllers\National\Eregistry\MovementController::class);
    

    
    Route::resource('boards', \App\Http\Controllers\National\Eregistry\EregistryBoradController::class, ['only' => ['index']]);
   

    // Add additional routes here if needed, such as PDF download or specific actions
});

// use Diglactic\Breadcrumbs\Breadcrumbs;
// use Diglactic\Breadcrumbs\Generator as BreadcrumbsGenerator;

// Breadcrumbs::for('dashboard', function (BreadcrumbsGenerator $trail) {
//     $trail->push('dashboard', route('dashboard'));
// });

// Breadcrumbs::for('registry.folders.index', function (BreadcrumbsGenerator $trail) {
//     $trail->parent('dashboard');
//     $trail->push('Folders', route('registry.folders.index'));
// });

// Breadcrumbs::for('registry.files.index', function (BreadcrumbsGenerator $trail) {
//     $trail->parent('registry.folders.index');
//     $trail->push('Files', route('registry.files.index'));
// });

