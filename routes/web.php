<?php

use App\Http\Controllers\National\Eregistry\DivisionController;
use App\Http\Controllers\National\Eregistry\DispatchController;
use App\Http\Controllers\National\Eregistry\EregistryBoradController;
use App\Http\Controllers\National\Eregistry\FileAccessController;
use App\Http\Controllers\National\Eregistry\FileCirculationController;
use App\Http\Controllers\National\Eregistry\FileController;
use App\Http\Controllers\National\Eregistry\FileTypeController;
use App\Http\Controllers\National\Eregistry\OrganisationController;
use App\Http\Controllers\National\Eregistry\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('user')) {
            return view('national.eregistry.circulations.reviewIndex');
        }

        return view('national.eregistry.index');
    })->name('dashboard');
});

Route::group([
    'as' => 'registry.',
    'prefix' => 'registry',
    'middleware' => ['auth'],
], function () {

    // Organisation Routes
    Route::match(['get', 'post'], 'organisations/datatables', [OrganisationController::class, 'getDataTables'])->name('organisations.datatables');
    Route::resource('organisations', OrganisationController::class);
    Route::get('/organisations/{id}/review-officer', [OrganisationController::class, 'showReviewOfficer'])->name('organisations.reviewOfficer.show');
    Route::patch('/organisations/{id}/update-review-officer', [OrganisationController::class, 'updateReviewOfficer'])->name('organisations.reviewOfficer.update');


    // Division Routes
    Route::match(['get', 'post'], 'divisions/datatables', [DivisionController::class, 'getDataTables'])->name('divisions.datatables');
    Route::resource('divisions', DivisionController::class);

    
    // File Routes
    // Route for serving the actual file content
    Route::get('files/{id}/view', [FileController::class, 'viewFile'])->name('files.view');
    Route::get('files/{id}/download', [FileController::class, 'downloadMain'])->name('files.download.main');
    Route::get('files/{id}/download-additional/{number}', [FileController::class, 'downloadAdditionalFile'])->name('files.download.additional');
    
    Route::get('files/create-type/{createType}', [FileController::class, 'createType'])->name('files.create.withType');
    Route::get('files/{id}/edit-type/{editType}', [FileController::class, 'editType'])->name('files.edit.withType');


    // Route::match(['get', 'post'], 'files/received/datatables', [FileController::class, 'getReceivedFilesDataTable'])->name('files.received.datatables');
    Route::match(['get', 'post'], 'files/datatables', [FileController::class, 'getArchiveFiles'])->name('files.archive.datatables');
    Route::resource('files', FileController::class);
    Route::post('files/archive', [FileController::class, 'archive'])->name('files.archive');


    // File Type Routes
    Route::get('/registry/file-types/{fileTypeId}/dynamic-form', [
        'as' => 'file-types.dynamic-form',
        'uses' => 'App\Http\Controllers\National\Eregistry\FileTypeController@dynamicForm'
    ]);
    Route::match(['get', 'post'], 'file-types/datatables', [FileTypeController::class, 'getDataTables'])->name('file-types.datatables');
    Route::resource('file-types', FileTypeController::class);


    // File Access Routes
    Route::match(['get', 'post'], 'file-access/datatables', [FileAccessController::class, 'getDataTables'])->name('file-access.datatables');
    Route::resource('file-access', FileAccessController::class);


    // Dispatch Routes
    Route::get('dispatches/userIndex', [DispatchController::class, 'userIndex'])->name('dispatches.user.index');

    Route::match(['get', 'post'], 'dispatches/datatables', [DispatchController::class, 'getDataTables'])->name('dispatches.datatables');
    Route::resource('dispatches', DispatchController::class);
    Route::match(['get', 'post'], 'user-dispatches/datatables', [DispatchController::class, 'getUserDataTables'])->name('dispatches.user.datatables');
    // Route::get('user', [DispatchController::class, 'userIndex'])->name('dispatches.user.index');
    
    
    // File Circulation Routes
    Route::match(['get', 'post'], 'file-circulations/initial/datatables', [FileCirculationController::class, 'getDataTables'])->name('file-circulations.datatables');
    Route::match(['get', 'post'], 'file-circulations/review/datatables', [FileCirculationController::class, 'getReviewDataTables'])->name('file-circulations.reviews.datatables');
    Route::match(['get', 'post'], 'file-circulations/assigned/datatables', [FileCirculationController::class, 'getAssignedDataTables'])->name('file-circulations.assigned.datatables');
    
    Route::resource('file-circulations', FileCirculationController::class);
    Route::get('/file-circulations/review/index', [FileCirculationController::class, 'reviewIndex'])->name('file-circulations.review.index');
    Route::get('/file-circulations/{fileCirculation}/review', [FileCirculationController::class, 'reviewFile'])->name('file-circulations.review.file');
    Route::patch('/file-circulations/{fileCirculation}/store/assigned-officers/', [FileCirculationController::class, 'storeAssignedOfficers'])->name('file-circulations.store.assigned-officers');
    Route::get('/file-circulations/assigned/index', [FileCirculationController::class, 'assignedIndex'])->name('file-circulations.assigned.index');
    Route::patch('/file-circulations/{fileCirculation}/store/complete', [FileCirculationController::class, 'storeComplete'])->name('file-circulations.store.complete');


    // User Routes
    Route::match(['get', 'post'], 'users/datatables', [UserController::class, 'getDataTables'])->name('users.datatables');
    Route::resource('users', UserController::class);


    // Eregistry Board Routes
    Route::get('boards', [EregistryBoradController::class, 'index'])->name('boards.index');
    Route::get('boards/myFiles', [EregistryBoradController::class, 'myFiles'])->name('boards.myFiles');
    Route::get('boards/management', [EregistryBoradController::class, 'management'])->name('boards.management');
    Route::get('boards/profile', [EregistryBoradController::class, 'profile'])->name('boards.profile');

    // Add additional routes here if needed, such as PDF download or specific actions

});



