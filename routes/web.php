<?php

use App\Http\Controllers\National\Eregistry\ActivityLogController;
use App\Http\Controllers\National\Eregistry\DispatchController;
use App\Http\Controllers\National\Eregistry\DivisionController;
use App\Http\Controllers\National\Eregistry\EregistryBoradController;
use App\Http\Controllers\National\Eregistry\FileAccessController;
use App\Http\Controllers\National\Eregistry\FileAssignmentController;
use App\Http\Controllers\National\Eregistry\FileCirculationController;
use App\Http\Controllers\National\Eregistry\FileController;
use App\Http\Controllers\National\Eregistry\FileTypeController;
use App\Http\Controllers\National\Eregistry\MinistryController;
use App\Http\Controllers\National\Eregistry\UserController;
use App\Http\Controllers\National\Eregistry\ExternalPartnerController;
use App\Http\Controllers\National\Eregistry\UfsApprovalController;
use App\Http\Controllers\National\Eregistry\IdentityOrganisationController;
use App\Http\Controllers\National\Eregistry\DocumentOverlayController;
use App\Models\National\Eregistry\Ministry;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [EregistryBoradController::class, 'index'])->name('dashboard');
});


Route::group([
    'as' => 'registry.',
    'prefix' => 'registry',
    'middleware' => ['auth'],
], function () {

    // Ministry Routes
    Route::match(['get', 'post'], 'ministries/datatables', [MinistryController::class, 'getDataTables'])->name('ministries.datatables');
    Route::resource('ministries', MinistryController::class);

    Route::match(['get', 'post'], 'organisations/datatables', [IdentityOrganisationController::class, 'getDataTables'])->name('organisations.datatables');
    Route::resource('organisations', IdentityOrganisationController::class);


    // Division Routes
    Route::match(['get', 'post'], 'divisions/datatables', [DivisionController::class, 'getDataTables'])->name('divisions.datatables');
    Route::resource('divisions', DivisionController::class);

    
    // File Routes
    Route::match(['get', 'post'], 'files/datatables', [FileController::class, 'getDataTables'])->name('files.datatables');
    Route::match(['get', 'post'], 'files/assigned/datatables', [FileController::class, 'getAssignedDataTables'])->name('files.assigned.datatables');


    Route::get('files', [FileController::class, 'index'])->name('files.index');
    Route::get('files/assigned', [FileController::class, 'assignedIndex'])->name('files.assigned.index');
    Route::resource('files', FileController::class)->except(['index']);
    Route::get('/files/{file}/audits', [FileController::class, 'viewAudit'])->name('files.audits');
    Route::get('files/{file}/pdf', [FileController::class, 'exportPdf'])->name('files.pdf');

    
    Route::get('files/{id}/view', [FileController::class, 'viewFile'])->name('files.view');
    Route::get('files/{id}/download', [FileController::class, 'download'])->name('files.download.main');
    Route::get('files/{id}/download-additional/{number}', [FileController::class, 'downloadAdditionalFile'])->name('files.download.additional');
    

    // Route::match(['get', 'post'], 'files/datatables', [FileController::class, 'getArchiveFiles'])->name('files.archive.datatables');
    Route::post('files/{file}/archive', [FileController::class, 'archive'])->name('files.archive');
    Route::post('files/{file}/close', [FileController::class, 'close'])->name('files.close');
    Route::get('files/archives/by-organisation/{id}', [FileController::class, 'filesByOrganisation']);

  
    // File Type Routes
    // Route::get('/registry/file-types/{fileTypeId}/dynamic-form', [
    //     'as' => 'file-types.dynamic-form',
    //     'uses' => 'App\Http\Controllers\National\Eregistry\FileTypeController@dynamicForm'
    // ]);
    Route::get('/file-types/name/suggestions', [FileTypeController::class, 'suggestions'])->name('file-types.name.suggestions');
    Route::get('/file-types/code/suggestions', [FileTypeController::class, 'codeSuggestions'])->name('file-types.code.suggestions');


    Route::match(['get', 'post'], 'file-types/datatables', [FileTypeController::class, 'getDataTables'])->name('file-types.datatables');
    Route::resource('file-types', FileTypeController::class);


    Route::match(['get', 'post'], 'external-partners/datatables', [ExternalPartnerController::class, 'getDataTables'])->name('external-partners.datatables');
    Route::resource('external-partners', ExternalPartnerController::class);
    

    // File Access Routes
    Route::match(['get', 'post'], 'file-access/datatables', [FileAccessController::class, 'getDataTables'])->name('file-access.datatables');
    Route::resource('file-access', FileAccessController::class);


    // Dispatch Routes
    Route::get('dispatches/userIndex', [DispatchController::class, 'userIndex'])->name('dispatches.user.index');
    Route::match(['get', 'post'], 'dispatches/datatables', [DispatchController::class, 'getDataTables'])->name('dispatches.datatables');
    Route::resource('dispatches', DispatchController::class);
    Route::match(['get', 'post'], 'user-dispatches/datatables', [DispatchController::class, 'getUserDataTables'])->name('dispatches.user.datatables');
    
    
    // File Circulation Routes
    Route::match(['get', 'post'], 'file-circulations/initial/datatables', [FileCirculationController::class, 'getDataTables'])->name('file-circulations.datatables');
    Route::match(['get', 'post'], 'file-circulations/review/datatables', [FileCirculationController::class, 'getReviewDataTables'])->name('file-circulations.reviews.datatables');
    Route::match(['get', 'post'], 'file-circulations/assigned/datatables', [FileCirculationController::class, 'getAssignedDataTables'])->name('file-circulations.assigned.datatables');
    Route::match(['get', 'post'], 'file-circulations/activity/datatables', [FileCirculationController::class, 'getActivityDataTables'])->name('file-circulations.activity.datatables');
    Route::match(['get', 'post'], 'file-circulations/sec/reviews/datatables', [FileCirculationController::class, 'getAllReviewDataTables'])->name('file-circulations.all.reviews.datatables');

    
    Route::resource('file-circulations', FileCirculationController::class);
    Route::get('/file-circulations/review/index', [FileCirculationController::class, 'reviewIndex'])->name('file-circulations.review.index');
    Route::get('/file-circulations/{fileCirculation}/review', [FileCirculationController::class, 'reviewFile'])->name('file-circulations.review.file');
    Route::patch('/file-circulations/{fileCirculation}/store/assigned-officers/', [FileCirculationController::class, 'storeAssignedOfficers'])->name('file-circulations.store.assigned-officers');
    Route::get('/file-circulations/assigned/index', [FileCirculationController::class, 'assignedIndex'])->name('file-circulations.assigned.index');
    Route::get('/file-circulations/activity/index', [FileCirculationController::class, 'activityIndex'])->name('file-circulations.activity.index');

    Route::patch('/file-circulations/{fileCirculation}/receive', [FileCirculationController::class, 'receive'])->name('file-circulations.receive');

    Route::post('/file-circulations/{fileCirculation}/ufs-approve', [UfsApprovalController::class, 'approve'])->name('ufs.approve');
    Route::post('/file-circulations/{fileCirculation}/ufs-reject', [UfsApprovalController::class, 'reject'])->name('ufs.reject');

    Route::get('/file-circulations/reviews/all/index', [FileCirculationController::class, 'allReceivedIndex'])->name('file-circulations.all.reviews.index'); //for secretary, minister
    Route::patch('/file-circulations/{fileCirculation}/store/complete', [FileCirculationController::class, 'storeComplete'])->name('file-circulations.store.complete');

    Route::get('/file-circulations/{fileCirculation}/overlays/edit', [DocumentOverlayController::class, 'edit'])->name('overlays.edit');

    Route::post('/file-circulations/{fileCirculation}/overlays/save', [DocumentOverlayController::class, 'save'])->name('overlays.save');

    Route::post('/file-circulations/{fileCirculation}/overlays/finalize', [DocumentOverlayController::class, 'finalize'])->name('overlays.finalize');

    Route::prefix('file-circulations/{fileCirculation}')->group(function () {
        Route::post('/assign', [FileAssignmentController::class, 'assign'])->name('file.assign');
        Route::post('/reassign', [FileAssignmentController::class, 'reassign'])->name('file.reassign');
    });

    // User Routes
    Route::match(['get', 'post'], 'users/datatables', [UserController::class, 'getDataTables'])->name('users.datatables');
    Route::get('users/edit/signature', [UserController::class, 'editSignature'])->name('users.signature.edit');
    Route::patch('users/update/signature', [UserController::class, 'updateSignature'])->name('users.signature.update');
    Route::get('users/edit-review-officer', [UserController::class, 'editReviewOfficer'])->name('users.edit-review-officer');
    Route::patch('users/update-review-officer', [UserController::class, 'updateReviewOfficer'])->name('users.update-review-officer');

    Route::resource('users', UserController::class);


    // Activity Log Routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');

    // Eregistry Board Routes
    Route::get('boards', [EregistryBoradController::class, 'index'])->name('boards.index');
    Route::get('boards/myFiles', [EregistryBoradController::class, 'myFiles'])->name('boards.myFiles');
    Route::get('boards/management', [EregistryBoradController::class, 'management'])->name('boards.management');
    Route::get('boards/profile', [EregistryBoradController::class, 'profile'])->name('boards.profile');

    // Add additional routes here if needed, such as PDF download or specific actions

});



