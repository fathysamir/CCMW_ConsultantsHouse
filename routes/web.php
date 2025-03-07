<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AccountController;
use App\Http\Controllers\Dashboard\AccountDashboardController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\settings\ContractTagController;
use App\Http\Controllers\Dashboard\ProjectDashboardController;
use App\Http\Controllers\Dashboard\settings\ContractSettingController;
use App\Http\Controllers\Dashboard\settings\DocumentTypeController;
use App\Http\Controllers\Dashboard\settings\ProjectFolderController;
use App\Http\Controllers\Dashboard\DocumentController;
use App\Http\Controllers\Dashboard\FileController;
use App\Http\Controllers\Dashboard\FileDocumentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|Password
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', [AuthController::class, 'login_view'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register_view'])->name('login.view');
Route::post('/sign-up', [AuthController::class, 'sign_up'])->name('sign-up');
Route::get('/', function () {
    
    if(!auth()->user()){
        return redirect('/login');
    }else{
        return redirect('/accounts');
    }
});
Route::group(['middleware' => ['admin']], function () {
    Route::get('/accounts', [AccountController::class, 'index'])->name('home');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts/store', [AccountController::class, 'store'])->name('accounts.store');

    Route::get('/accounts/contract-tags', [ContractTagController::class, 'index'])->name('accounts.contract-tags');
    Route::get('/accounts/contract-tags/create', [ContractTagController::class, 'create'])->name('accounts.contract-tags.create');
    Route::post('/accounts/contract-tags/store', [ContractTagController::class, 'store'])->name('accounts.contract-tags.store');
    Route::get('/accounts/contract-tags/edit/{id}', [ContractTagController::class, 'edit'])->name('accounts.contract-tags.edit');
    Route::post('/accounts/contract-tags/update/{id}', [ContractTagController::class, 'update'])->name('accounts.contract-tags.update');
    Route::get('/accounts/contract-tags/delete/{id}', [ContractTagController::class, 'delete'])->name('accounts.contract-tags.delete');


    Route::get('/accounts/document-types', [DocumentTypeController::class, 'index'])->name('accounts.document-types');
    Route::get('/accounts/document-types/create', [DocumentTypeController::class, 'create'])->name('accounts.document-types.create');
    Route::post('/accounts/document-types/store', [DocumentTypeController::class, 'store'])->name('accounts.document-types.store');
    Route::get('/accounts/document-types/edit/{id}', [DocumentTypeController::class, 'edit'])->name('accounts.document-types.edit');
    Route::post('/accounts/document-types/update/{id}', [DocumentTypeController::class, 'update'])->name('accounts.document-types.update');
    Route::get('/accounts/document-types/delete/{id}', [DocumentTypeController::class, 'delete'])->name('accounts.document-types.delete');

    Route::get('/accounts/project-folders', [ProjectFolderController::class, 'index'])->name('accounts.project-folders');
    Route::get('/accounts/project-folders/create', [ProjectFolderController::class, 'create'])->name('accounts.project-folders.create');
    Route::post('/accounts/project-folders/store', [ProjectFolderController::class, 'store'])->name('accounts.project-folders.store');
    Route::get('/accounts/project-folders/edit/{id}', [ProjectFolderController::class, 'edit'])->name('accounts.project-folders.edit');
    Route::post('/accounts/project-folders/update/{id}', [ProjectFolderController::class, 'update'])->name('accounts.project-folders.update');
    Route::get('/accounts/project-folders/delete/{id}', [ProjectFolderController::class, 'delete'])->name('accounts.project-folders.delete');


    Route::get('/accounts/contract-settings/{id}', [ContractSettingController::class, 'index'])->name('accounts.contract-settings');
    Route::get('/accounts/contract-settings/{id}/create', [ContractSettingController::class, 'create'])->name('accounts.contract-settings.create');
    Route::post('/accounts/contract-settings/store', [ContractSettingController::class, 'store'])->name('accounts.contract-settings.store');
    Route::get('/accounts/contract-settings/edit/{id}', [ContractSettingController::class, 'edit'])->name('accounts.contract-settings.edit');
    Route::post('/accounts/contract-settings/update/{id}', [ContractSettingController::class, 'update'])->name('accounts.contract-settings.update');
    Route::get('/accounts/contract-settings/delete/{id}', [ContractSettingController::class, 'delete'])->name('accounts.contract-settings.delete');

    Route::get('/switch-account/{id}', function ($id) {
        //session(['current_account_id' => $id]);
        $user=auth()->user();
        $user->current_account_id=$id;
        $user->save();
        return redirect()->route('account.home');
    })->where('id', '[0-9]+')->name('switch.account');
    Route::get('/account', [AccountDashboardController::class, 'index'])->where('id', '[0-9]+')->name('account.home');
    Route::get('/account/EPS', [AccountDashboardController::class, 'EPS'])->name('account.EPS');

    Route::get('/account/contract-tags', [ContractTagController::class, 'index'])->name('account.contract-tags');
    Route::get('/account/contract-tags/create', [ContractTagController::class, 'create'])->name('account.contract-tags.create');
    Route::get('/account/contract-tags/edit/{id}', [ContractTagController::class, 'edit'])->name('account.contract-tags.edit');
    Route::get('/account/document-types', [DocumentTypeController::class, 'index'])->name('account.document-types');
    Route::get('/account/document-types/create', [DocumentTypeController::class, 'create'])->name('account.document-types.create');
    Route::get('/account/document-types/edit/{id}', [DocumentTypeController::class, 'edit'])->name('account.document-types.edit');
    Route::get('/account/contract-settings/{id}', [ContractSettingController::class, 'index'])->name('account.contract-settings');
    Route::get('/account/contract-settings/{id}/create', [ContractSettingController::class, 'create'])->name('account.contract-settings.create');
    Route::get('/account/contract-settings/edit/{id}', [ContractSettingController::class, 'edit'])->name('account.contract-settings.edit');
    Route::get('/account/project-folders', [ProjectFolderController::class, 'index'])->name('account.project-folders');
    Route::get('/account/project-folders/create', [ProjectFolderController::class, 'create'])->name('account.project-folders.create');
    Route::get('/account/project-folders/edit/{id}', [ProjectFolderController::class, 'edit'])->name('account.project-folders.edit');


    Route::get('/getChildrenEPS', [AccountDashboardController::class, 'getChildrenEPS'])->name('getChildrenEPS');
    Route::get('/getProjectsEPS', [AccountDashboardController::class, 'getProjectsEPS'])->name('getProjectsEPS');

    Route::post('/deleteChildrenEPS', [AccountDashboardController::class, 'deleteChildrenEPS'])->name('deleteChildrenEPS');
    Route::post('/store_EPS', [AccountDashboardController::class, 'store_EPS'])->name('store_EPS');
    Route::post('/rename_EPS', [AccountDashboardController::class, 'rename_EPS'])->name('rename_EPS');
    Route::get('/account/create-project', [AccountDashboardController::class, 'create_project_view'])->name('account.create_project_view');
    Route::post('/account/store-project', [AccountDashboardController::class, 'store_project'])->name('store_project');
    Route::get('/account/projects', [ProjectController::class, 'index'])->name('account.projects');
    Route::get('/account/projects/create-project', [ProjectController::class, 'create_project_view'])->name('projects.create_project_view');
    Route::post('/account/projects/store-project', [ProjectController::class, 'store_project'])->name('projects.store_project');

    Route::get('/account/edit-project/{id}', [ProjectController::class, 'edit_project_view'])->name('account.edit_project_view');
    Route::post('/account/update-project/{project}', [ProjectController::class, 'update_project'])->name('projects.update_project');
    Route::post('/archiveProject', [ProjectController::class, 'archiveProject'])->name('archiveProject');
    Route::post('/deleteProject', [ProjectController::class, 'deleteProject'])->name('deleteProject');
    Route::post('/restoreProject', [ProjectController::class, 'restoreProject'])->name('restoreProject');


    Route::get('/switch-project/{id}', function ($id) {
        //session(['current_account_id' => $id]);
        $user=auth()->user();
        $user->current_project_id=$id;
        $user->save();
        return redirect()->route('project.home');
    })->where('id', '[0-9]+')->name('switch.project');
    Route::get('/project', [ProjectDashboardController::class, 'index'])->where('id', '[0-9]+')->name('project.home');
    Route::get('/project/contract-tags', [ContractTagController::class, 'index'])->name('project.contract-tags');
    Route::get('/project/contract-tags/create', [ContractTagController::class, 'create'])->name('project.contract-tags.create');
    Route::get('/project/contract-tags/edit/{id}', [ContractTagController::class, 'edit'])->name('project.contract-tags.edit');
    Route::get('/project/document-types', [DocumentTypeController::class, 'index'])->name('project.document-types');
    Route::get('/project/document-types/create', [DocumentTypeController::class, 'create'])->name('project.document-types.create');
    Route::get('/project/document-types/edit/{id}', [DocumentTypeController::class, 'edit'])->name('project.document-types.edit');
    Route::get('/project/contract-settings/{id}', [ContractSettingController::class, 'index'])->name('project.contract-settings');
    Route::get('/project/contract-settings/{id}/create', [ContractSettingController::class, 'create'])->name('project.contract-settings.create');
    Route::get('/project/contract-settings/edit/{id}', [ContractSettingController::class, 'edit'])->name('project.contract-settings.edit');
    Route::get('/project/project-folders', [ProjectFolderController::class, 'index'])->name('project.project-folders');
    Route::get('/project/project-folders/create', [ProjectFolderController::class, 'create'])->name('project.project-folders.create');
    Route::get('/project/project-folders/edit/{id}', [ProjectFolderController::class, 'edit'])->name('project.project-folders.edit');

    Route::get('/project/upload-single-doc/create', [DocumentController::class, 'create_single_doc_view'])->name('project.upload_single_doc.create');
    Route::post('/project/upload-single-doc/store', [DocumentController::class, 'store_single_doc'])->name('project.upload_single_doc.store');
    Route::post('/upload-single-file', [DocumentController::class, 'upload_single_doc'])->name('upload_single_doc');
    Route::get('/project/all-documents', [DocumentController::class, 'all_documents'])->name('project.all_documents.index');
    Route::get('/project/document/edit/{id}', [DocumentController::class, 'edit_document'])->name('project.edit-document');
    Route::post('/project/document/update/{id}', [DocumentController::class, 'update_document'])->name('project.update-document');
    Route::post('/project/document/change-owner', [DocumentController::class, 'changeOwner'])->name('project.document.change-owner');
    Route::get('/project/folder/get-files/{folderId}', [DocumentController::class, 'getFolderFiles'])->name('project.folders.getFiles');
    Route::post('/project/document/assign-document', [DocumentController::class, 'assignDocument'])->name('project.document.assign-document');
    Route::get('/project/document/delete/{id}', [DocumentController::class, 'delete'])->name('project.document.delete');
    Route::post('/project/change-owner-for-all', [DocumentController::class, 'changeOwnerForAll'])->name('project.document.change-owner-for-all');
    Route::post('/project/change-stake-holders-for-all', [DocumentController::class, 'changeStakeHoldersForAll'])->name('project.document.change-stake-holders-for-all');
    Route::post('/project/change-doc-type-for-all', [DocumentController::class, 'changeDocTypeForAll'])->name('project.document.change-doc-type-for-all');
    Route::post('/project/delete-selected-docs', [DocumentController::class, 'deleteSelectedDocs'])->name('project.document.delete-selected-docs');
    Route::post('/project/assign-to-file-for-all', [DocumentController::class, 'assignToFileForAll'])->name('project.document.assign-to-file-for-all');


    Route::get('/switch-folder/{id}', function ($id) {
        //session(['current_account_id' => $id]);
        $user=auth()->user();
        $user->current_folder_id=$id;
        $user->save();
        return redirect()->route('project.files');
    })->where('id', '[0-9]+')->name('switch.folder');
    Route::get('/project/files', [FileController::class, 'index'])->name('project.files');
    Route::get('/project/files/create', [FileController::class, 'create'])->name('project.files.create');
    Route::post('/project/files/store', [FileController::class, 'store'])->name('project.files.store');
    Route::get('/project/files/edit/{id}', [FileController::class, 'edit'])->name('project.files.edit');
    Route::post('/project/files/update/{id}', [FileController::class, 'update'])->name('project.files.update');
    Route::get('/project/files/delete/{id}', [FileController::class, 'delete'])->name('project.files.delete');
    Route::get('/project/files/archive/{id}', [FileController::class, 'archive'])->name('project.files.archive');
    Route::post('/project/file/change-owner', [FileController::class, 'changeOwner'])->name('project.file.change-owner');

    Route::get('/project/file/{id}/documents', [FileDocumentController::class, 'index'])->name('project.file-documents.index');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    
        Route::any('/users', [UserController::class, 'index'])->name('users'); 
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('edit.user');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('update.user');
        Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('delete.user');
});