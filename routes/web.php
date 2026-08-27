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
use App\Http\Controllers\National\Eregistry\AuthController;
use App\Http\Controllers\National\Eregistry\UnitController;
use App\Models\National\Eregistry\Ministry;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/login', [AuthController::class, 'redirect'])
//     ->middleware('guest')
//     ->name('login');

// Route::get('/', function () {
//     return auth()->check()
//         ? redirect()->route('dashboard')
//         : redirect()->route('login');
// });

// Route::get('/login', function () {
//     if (auth()->check()) {
//         return redirect()->route('dashboard');
//     }

//     return app(AuthController::class)->redirect();
// })->name('login');


// Route::get('/auth/callback', [AuthController::class, 'callback'])
//     ->name('keycloak.callback');


// Route::post('/logout', [AuthController::class, 'logout'])
//     ->middleware('auth')
//     ->name('logout');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', [EregistryBoradController::class, 'index'])->name('dashboard');
});

// Route::middleware('auth')->group(function () {
//         Route::get('/dashboard', fn () => view('dashboard'));
//         Route::get('/profile', fn () => view('profile'));
//     });

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
    Route::get('divisions/{division}/assign-hod', [DivisionController::class, 'assignHod'])->name('divisions.assign-hod');
    Route::put('divisions/{division}/update-hod', [DivisionController::class, 'updateHod'])->name('divisions.update-hod');
    Route::resource('divisions', DivisionController::class);

    
    // Unit Routes
    Route::match(['get', 'post'], 'units/datatables', [UnitController::class, 'getDataTables'])->name('units.datatables');
    Route::get('units/create/{division_id}', [UnitController::class, 'create'])->name('units.create');
    Route::resource('units', UnitController::class)->except(['create']);
    
    
    // File Routes
    Route::match(['get', 'post'], 'files/datatables', [FileController::class, 'getDataTables'])->name('files.datatables');
    Route::get('files/{file}/preview', [FileController::class, 'preview'])->name('files.preview');
    Route::get('files', [FileController::class, 'index'])->name('files.index');
    Route::get('files/{file}/audits', [FileController::class, 'viewAudit'])->name('files.audits');
    Route::get('files/{file}/pdf', [FileController::class, 'exportPdf'])->name('files.pdf');
    Route::resource('files', FileController::class)->except(['index']);
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('files/{file}/view', [FileController::class, 'viewOnlineFile'])->name('files.view.online');
   

    // Download routes for main and additional files
    Route::get('files/{id}/download', [FileController::class, 'download'])->name('files.download.main');
    Route::get('files/{file}/download-additional/{number}', [FileController::class, 'downloadAdditionalFile'])->name('files.download.additional');    

    Route::post('files/{file}/ufs', [FileController::class, 'ufsCirculate'])->name('files.ufsCirculate');
    Route::post('files/{file}/sign', [FileController::class, 'signFile'])->name('files.sign');
    Route::post('files/{file}/archive', [FileController::class, 'archive'])->name('files.archive');
    Route::post('files/{file}/close', [FileController::class, 'close'])->name('files.close');


    Route::get('/file-types/name/suggestions', [FileTypeController::class, 'suggestions'])->name('file-types.name.suggestions');
    Route::get('/file-types/code/suggestions', [FileTypeController::class, 'codeSuggestions'])->name('file-types.code.suggestions');


    Route::match(['get', 'post'], 'file-types/datatables', [FileTypeController::class, 'getDataTables'])->name('file-types.datatables');
    Route::patch('/file-types/{fileType}/activate', [FileTypeController::class, 'activate'])->name('file-types.activate');
    Route::patch('/file-types/{fileType}/deactivate', [FileTypeController::class, 'deactivate'])->name('file-types.deactivate');
    Route::resource('file-types', FileTypeController::class);


    Route::get('/external-partners/name/suggestions', [ExternalPartnerController::class, 'suggestions'])->name('external-partners.name.suggestions');
    Route::patch('/external-partners/{partner}/activate', [ExternalPartnerController::class, 'activate'])->name('external-partners.activate');
    Route::patch('/external-partners/{partner}/deactivate', [ExternalPartnerController::class, 'deactivate'])->name('external-partners.deactivate');
    Route::match(['get', 'post'], 'external-partners/datatables', [ExternalPartnerController::class, 'getDataTables'])->name('external-partners.datatables');
    Route::resource('external-partners', ExternalPartnerController::class);
    

    // File Access Routes
    Route::match(['get', 'post'], 'file-access/datatables', [FileAccessController::class, 'getDataTables'])->name('file-access.datatables');
    Route::resource('file-access', FileAccessController::class);


    // Dispatch Routes
    Route::get('dispatches/userIndex', [DispatchController::class, 'userIndex'])->name('dispatches.user.index');
    // Route::match(['get', 'post'], 'dispatches/datatables', [DispatchController::class, 'getDataTables'])->name('dispatches.datatables');
    Route::resource('dispatches', DispatchController::class);
    // Route::match(['get', 'post'], 'user-dispatches/datatables', [DispatchController::class, 'getUserDataTables'])->name('dispatches.user.datatables');
    
    Route::resource('file-circulations', FileCirculationController::class);

    Route::get('/file-circulations/review/index', [FileCirculationController::class, 'reviewIndex'])->name('file-circulations.review.index');

    Route::get('/file-circulations/{fileCirculation}/review', [FileCirculationController::class, 'reviewFile'])->name('file-circulations.review.file');
    Route::patch('/file-circulations/{fileCirculation}/receive', [FileCirculationController::class, 'receive'])->name('file-circulations.receive');
    Route::patch('/file-circulations/{fileCirculation}/store/complete', [FileCirculationController::class, 'storeComplete'])->name('file-circulations.store.complete');

    Route::post('/file-circulations/colleague/store', [FileCirculationController::class, 'colleagueStore'])->name('file-circulations.colleague.store');
    Route::post('/file-circulations/{fileCirculation}/colleague/update', [FileCirculationController::class, 'colleagueUpdate'])->name('file-circulations.colleague.update');

    Route::get('/file-circulations/assigned/index', [FileCirculationController::class, 'assignedIndex'])->name('file-circulations.assigned.index');
    Route::get('/file-circulations/activity/index', [FileCirculationController::class, 'activityIndex'])->name('file-circulations.activity.index');

    Route::post('/file-circulations/{fileCirculation}/ufs-approve', [UfsApprovalController::class, 'approve'])->name('ufs.approve');
    Route::post('/file-circulations/{fileCirculation}/ufs-reject', [UfsApprovalController::class, 'reject'])->name('ufs.reject');

    Route::get('/file-circulations/{fileCirculation}/overlays/edit', [DocumentOverlayController::class, 'edit'])->name('overlays.edit');
    Route::post('/file-circulations/{fileCirculation}/overlays/save', [DocumentOverlayController::class, 'save'])->name('overlays.save');
    Route::post('/file-circulations/{fileCirculation}/overlays/finalize', [DocumentOverlayController::class, 'finalize'])->name('overlays.finalize');

    Route::prefix('file-circulations/{fileCirculation}')->group(function () {
        Route::post('/review', [FileAssignmentController::class, 'review'])->name('file.review');
        Route::post('/assign', [FileAssignmentController::class, 'assign'])->name('file.assign');
        Route::post('/reassign', [FileAssignmentController::class, 'reassign'])->name('file.reassign');
        Route::post('/accept', [FileAssignmentController::class, 'accept'])->name('file.accept');
        Route::post('/complete', [FileAssignmentController::class, 'complete'])->name('file.complete');
    });

    // User Routes
    Route::match(['get', 'post'], 'users/datatables', [UserController::class, 'getDataTables'])->name('users.datatables');
   
    Route::get('users/edit/signature', [UserController::class, 'editSignature'])->name('users.signature.edit');
    Route::patch('users/update/signature', [UserController::class, 'updateSignature'])->name('users.signature.update');
    
    Route::get('users/edit-review-officer', [UserController::class, 'editReviewOfficer'])->name('users.edit-review-officer');
    Route::patch('users/update-review-officer', [UserController::class, 'updateReviewOfficer'])->name('users.update-review-officer');
    
    Route::get('users/edit-secretary', [UserController::class, 'editSecretary'])->name('users.edit-secretary');
    Route::patch('users/update-secretary', [UserController::class, 'updateSecretary'])->name('users.update-secretary');


    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::resource('users', UserController::class);

    Route::get('/login', fn () => view('login'))->name('login');
    Route::get('/auth/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
    Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
   

    // Activity Log Routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');

    // Eregistry Board Routes
    Route::get('boards', [EregistryBoradController::class, 'index'])->name('boards.index');
    Route::get('boards/myFiles', [EregistryBoradController::class, 'myFiles'])->name('boards.myFiles');
    Route::get('boards/management', [EregistryBoradController::class, 'management'])->name('boards.management');
    Route::get('boards/profile', [EregistryBoradController::class, 'profile'])->name('boards.profile');

    // Add additional routes here if needed, such as PDF download or specific actions

});



