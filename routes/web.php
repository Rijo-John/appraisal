<?php
use App\Http\Controllers\CommonController;
use App\Http\Controllers\AzureAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\AppraisalMasterController;
use App\Http\Controllers\AttributeReviewController;
use App\Http\Controllers\AttributeQuestionController; 
use App\Http\Controllers\AppraisalFormController;
use App\Http\Controllers\AppraisalNonTechnicalFormController;
use App\Http\Controllers\GoalController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\MyAppController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\AppraiserEvaluationNonTechnicalController;
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
    return view('welcome');
});

Route::get('/syncusers', [CommonController::class, 'syncUsers'])->name('syncusers');
Route::get('/syncprojects', [CommonController::class, 'syncProjects'])->name('syncprojects');

//Route::post('/syncdesignations', [CommonController::class, 'syncDesignations'])->name('syncdesignations');
Route::match(['get', 'post'], '/syncdesignations', [CommonController::class, 'syncDesignations'])->name('syncdesignations');

Route::get('/questions', [QuestionController::class, 'index']);
Route::post('/save-designation-questions', [QuestionController::class, 'saveDesignationQuestions']);

Route::get('/set-attribute-review', [AttributeReviewController::class, 'index']);
Route::post('/saveRatings', [AttributeReviewController::class, 'saveRatings']);


Route::get('/login', [AzureAuthController::class, 'redirectToAzure'])->name('login');
Route::get('/auth/azure', [AzureAuthController::class, 'redirectToAzure'])->name('azure.login');
Route::get('/auth/callback', [AzureAuthController::class, 'handleAzureCallback']);
Route::get('/nopermission', function () {
    return view('no_permission'); 
})->name('nopermission');
Route::middleware(['auth:web'])->group(function () {
    Route::post('/appraiser-goal-submit', [AppraiserEvaluationNonTechnicalController::class, 'submitAppraiserGoals'])->name('appraiserGoalSubmit');
    Route::get('/appraiserindex/{id}', [AppraiserEvaluationNonTechnicalController::class, 'index'])->name('appraiserevaluateindex');
    Route::get('/list', [ListController::class, 'list'])->name('list');
    Route::get('/cfGhjYer/{filename}', [AppraisalFormController::class, 'download'])->name('file.download');
    Route::post('/file/delete', [AppraisalFormController::class, 'delete'])->name('file.delete');
    Route::post('/delete_attachment', [AppraisalFormController::class, 'deleteAttachment'])->name('deleteAttachment');

    Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/attribute-designation', [AttributeQuestionController::class, 'index']);
    Route::post('/save-designation-questions', [AttributeQuestionController::class, 'saveDesignationQuestions']);
    Route::get('/set-attribute-review', [AttributeReviewController::class, 'index']);
    Route::post('/saveAttribureRatings', [AttributeReviewController::class, 'saveRatings']);

    Route::get('/employee-review-listing', [AppraisalFormController::class, 'reviewEmployeeList']);

    //Route::get('/employee-goal-listing', [GoalController::class, 'index']);
    Route::post('/employee-goal-submit', [AppraisalFormController::class, 'submitEmpGoals'])->name('employeeGoalSubmit');
    Route::get('/my-appraisal', [AppraisalFormController::class, 'index'])->name('myappraisal');
    Route::get('/my-appraisal-non-technical', [AppraisalNonTechnicalFormController::class, 'index'])->name('myappraisalnontechnical');
    Route::post('/employee-goal-submit-non-technical', [AppraisalNonTechnicalFormController::class, 'submitEmpGoalsNonTechnical'])->name('employeeGoalSubmitNonTechnical');

    Route::get('/my-app', [MyAppController::class, 'appraisal_project_wise'])->name('myapp');
    Route::post('/my-app-submit', [MyAppController::class, 'appraisal_project_wise_submit'])->name('myappsubmit');
    
    
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/administration', [AdministrationController::class, 'index'])->name('administration');
        Route::get('/assign-admin', [AdminController::class, 'showAssignAdmin'])->name('assign.admin');
        Route::get('/assign-admin/users', [AdminController::class, 'getUsers'])->name('assign.admin.users'); // New AJAX route
        Route::post('/assign-admin', [AdminController::class, 'assignAdmin'])->name('assign.admin.submit');

        

    });


    Route::middleware(['admin_or_superadmin'])->group(function () {
        /*Route::get('/appraisal-master', function () {
            return view('appraisal_master'); 
        })->name('appraisal.view');*/

        Route::get('/getappraisaldata', [AppraisalMasterController::class, 'getAppraisalData'])->name('appraisaldata');
        Route::get('/filter-appraisals', [AppraisalMasterController::class, 'filterAppraisalsByCycle'])->name('filterAppraisalsByCycle');
        Route::get('/export-appraisals', [AppraisalMasterController::class, 'exportAppraisalsToExcel'])->name('exportAppraisalsToExcel');

        Route::delete('/delete-appraisal/{id}', [AppraisalMasterController::class, 'deleteAppraisal'])->name('deleteAppraisal');

        Route::get('/edit-appraisal/{id}', [AppraisalMasterController::class, 'edit'])->name('edit-appraisal');
        Route::post('/update-appraisal/{id}', [AppraisalMasterController::class, 'update'])->name('update-appraisal');
        Route::post('/getappraisaluserscontent', [AppraisalMasterController::class, 'getAppraisalUsersContent'])->name('getappraisaluserscontent');
        Route::post('/send-appraisal-emails', [AppraisalMasterController::class, 'sendAppraisalEmails'])->name('sendAppraisalEmails');






        Route::match(['get', 'post'], '/syncappraisalusers', [CommonController::class, 'syncAppraisalUsers'])->name('syncappraisalusers');

        Route::get('/getSyncedAppraisalUsers', [CommonController::class, 'getSyncedAppraisalUsers'])->name('getSyncedAppraisalUsers');
        Route::post('/storeAppraisalUsers', [CommonController::class, 'storeAppraisalUsers'])->name('storeAppraisalUsers');
    });

    Route::get('/download/{filename}', function ($filename) {
        $filePath = "storage/app/public/uploads/evidence/" . $filename;

        if (!Storage::exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::download($filePath);
    })->name('download.file');

    Route::get('/refresh-certification', [AppraisalFormController::class, 'refreshCertification'])->name('refresh.certification');

    
});

