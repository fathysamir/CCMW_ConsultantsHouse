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
use App\Http\Controllers\Dashboard\ImportDocumentController;
use App\Http\Controllers\Dashboard\UploadGroupDocumentController;
use App\Models\ProjectFile;
use App\Models\FileDocument;
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

Route::get('/login', [AuthController::class, 'login_view'])->name('login_view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register_view'])->name('register_view');
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
    Route::get('/account/{id}', function ($id) {
        session(['current_edit_account' => $id]);
        
        return redirect()->route('account.edit');
    })->where('id', '[0-9]+');
    Route::get('/accounts/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/accounts/update', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account/delete/{id}', [AccountController::class, 'delete'])->name('account.delete');

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

    Route::get('/account/users', [AccountDashboardController::class, 'account_users'])->name('account.users');
    Route::get('/account/user/{id}', [AccountDashboardController::class, 'edit_user'])->name('account.edit-user');
    Route::post('/account/update-user/{id}', [AccountDashboardController::class, 'update_user'])->name('account.update-user');
    Route::get('/account/user/delete/{id}', [AccountDashboardController::class, 'delete_user'])->name('account.delete-user');

    Route::get('/getChildrenEPS', [AccountDashboardController::class, 'getChildrenEPS'])->name('getChildrenEPS');
    Route::get('/getProjectsEPS', [AccountDashboardController::class, 'getProjectsEPS'])->name('getProjectsEPS');

    Route::post('/deleteChildrenEPS', [AccountDashboardController::class, 'deleteChildrenEPS'])->name('deleteChildrenEPS');
    Route::post('/store_EPS', [AccountDashboardController::class, 'store_EPS'])->name('store_EPS');
    Route::post('/reorder_EPS', [AccountDashboardController::class, 'reorder_EPS'])->name('reorder_EPS');
    Route::post('/rename_EPS', [AccountDashboardController::class, 'rename_EPS'])->name('rename_EPS');
    Route::get('/account/create-project', [AccountDashboardController::class, 'create_project_view'])->name('account.create_project_view');
    Route::post('/account/store-project', [AccountDashboardController::class, 'store_project'])->name('store_project');
    Route::get('/account/projects', [ProjectController::class, 'index'])->name('account.projects');
    Route::get('/account/projects/create-project', [ProjectController::class, 'create_project_view'])->name('projects.create_project_view');
    Route::post('/account/projects/store-project', [ProjectController::class, 'store_project'])->name('projects.store_project');
    Route::post('/account/send-invitation', [AccountDashboardController::class, 'send_invitation'])->name('send_invitation');

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
    Route::post('/project/assign_users', [ProjectDashboardController::class, 'assign_users'])->name('assign_users');

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
    Route::get('/document/get-files/{id}', [DocumentController::class, 'get_assigned_files'])->name('get_assigned_files');
    Route::post('/get-documents-by-thread', [DocumentController::class, 'getDocsByReference']);
    Route::post('/project/document/assign-document-bySlug', [DocumentController::class, 'assignDocumentbyslug'])->name('project.document.assign-document-slug');

    Route::get('go-to-fileDocument/{docId}/{fileId}',function($doc,$file){
       
        $document=FileDocument::where('file_id',$file)->where('document_id',$doc)->first();
        $file=ProjectFile::findOrFail($file);
        session(['specific_file_doc' => $document->id]);
        return redirect()->route('project.file-documents.index',$file->slug);
    })->name('goToDocFile');
    Route::post('/get-files-by-document', [DocumentController::class, 'getFilesByDoc']);

    Route::get('/download-document/{id}', [DocumentController::class, 'downloadDocument'])->name('download.document');
    Route::get('/project/file-docs/{doc}/doc/{id}/edit', function ($doc,$id) {
        session(['current_view' => 'file_doc']);
        session(['current_file_doc' => $doc]);
        
        return redirect()->route('project.edit-document',$id);
    });

    Route::get('/project/files_file/{fil}/doc/{doc}/edit/{id}', function ($fil,$doc,$id) {
        session(['current_view' => 'file']);
        session(['current_file2' => $fil]);
        session(['specific_file_doc' => $doc]);
        return redirect()->route('project.edit-document',$id);
    });

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
    Route::get('/project/file-document-first-analyses/{id}', [FileDocumentController::class, 'file_document_first_analyses'])->name('project.file-document-first-analyses');
    Route::post('/project/file-document-first-analyses/store/{id}', [FileDocumentController::class, 'store_file_document_first_analyses'])->name('project.file-document-first-analyses.store');
    Route::post('/project/upload-editor-image', [FileDocumentController::class, 'upload_editor_image'])->name('upload_editor_image');
    Route::post('/export-word-claim-docs', [FileDocumentController::class, 'exportWordClaimDocs']);
    Route::post('/project/copy_move_doc_to_another_file', [FileDocumentController::class, 'copy_move_doc_to_another_file'])->name('copy_move_doc_to_another_file');
    Route::post('/project/unassign-doc', [FileDocumentController::class, 'unassign_doc'])->name('project.unassign_doc');
    Route::post('/project/delete-doc-from-cmw-entirely', [FileDocumentController::class, 'delete_doc_from_cmw_entirely'])->name('project.delete_doc_from_cmw_entirely');
    Route::post('/project/doc/make-for-claim', [FileDocumentController::class, 'change_for_claimOrNoticeOrChart'])->name('project.change_for_claimOrNoticeOrChart');
    Route::post('/download-all-documents', [FileDocumentController::class, 'download_documents'])->name('download.all-documents');
    Route::post('/download-specific-documents', [FileDocumentController::class, 'download_specific_documents'])->name('download.download_specific_documents');
    Route::post('/project/edit-docs-info', [FileDocumentController::class, 'edit_docs_info'])->name('download.edit_docs_info');
    Route::post('/project/change-flag', [FileDocumentController::class, 'change_flag'])->name('change-flag');

    Route::get('/project/import-documents', [ImportDocumentController::class, 'import_docs_view'])->name('import_docs_view');
    Route::post('/upload-import-excel-file', [ImportDocumentController::class, 'upload_import_excel_file'])->name('upload_import_excel_file');
    Route::post('/upload-multi-files', [ImportDocumentController::class, 'upload_multi_files'])->name('upload_multi_files');
    Route::post('/get-headers', [ImportDocumentController::class, 'getHeaders'])->name('getHeaders');
    Route::post('/start-import', [ImportDocumentController::class, 'start_import'])->name('start_import');

    Route::get('/project/upload-group-documents', [UploadGroupDocumentController::class, 'index'])->name('project.upload-group-documents');
    Route::get('/formate_date', [UploadGroupDocumentController::class, 'formate_date'])->name('formate_date');
    Route::post('/group-documents/upload-multi-files', [UploadGroupDocumentController::class, 'upload_multi_files'])->name('group-documents.upload_multi_files');
    Route::post('/group-documents/save-documents', [UploadGroupDocumentController::class, 'saveDocuments'])->name('group-documents.saveDocuments');
    Route::get('/group-documents/document/{id}', [UploadGroupDocumentController::class, 'view_doc'])->name('group-documents.view_doc');
    Route::post('/group-documents/update-test-document/{id}', [UploadGroupDocumentController::class, 'update_test_document'])->name('project.upload-group-documents.update-test-document');
    Route::get('/group-documents/check_test_documents', [UploadGroupDocumentController::class, 'check_test_documents'])->name('group-documents.check_test_documents');
    Route::get('/group-documents/import_group_documents', [UploadGroupDocumentController::class, 'import_group_documents'])->name('group-documents.import_group_documents');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::post('/change-sideBarTheme', [AuthController::class, 'change_sideBarTheme'])->name('change_sideBarTheme');

    
        Route::any('/users', [UserController::class, 'index'])->name('users'); 
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('edit.user');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('update.user');
        Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('delete.user');
});