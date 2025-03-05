<?php
use App\Http\Controllers\CommonController;
use App\Http\Controllers\AzureAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppraisalMasterController;
use App\Http\Controllers\AttributeReviewController;
use App\Http\Controllers\AttributeQuestionController; 
use App\Http\Controllers\AppraisalFormController;
use App\Http\Controllers\GoalController;
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

Route::middleware(['auth:web'])->group(function () {
    Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/attribute-designation', [AttributeQuestionController::class, 'index']);
    Route::post('/save-designation-questions', [AttributeQuestionController::class, 'saveDesignationQuestions']);
    Route::get('/set-attribute-review', [AttributeReviewController::class, 'index']);
    Route::post('/saveAttribureRatings', [AttributeReviewController::class, 'saveRatings']);

    Route::get('/employee-review-listing', [AppraisalFormController::class, 'reviewEmployeeList']);

    Route::get('/employee-goal-listing', [GoalController::class, 'index']);
    Route::post('/employee-goal-submit', [GoalController::class, 'submitEmpGoals'])->name('employeeGoalSubmit');


    Route::middleware(['superadmin'])->group(function () {
        Route::get('/assign-admin', [AdminController::class, 'showAssignAdmin'])->name('assign.admin');
        Route::get('/assign-admin/users', [AdminController::class, 'getUsers'])->name('assign.admin.users'); // New AJAX route
        Route::post('/assign-admin', [AdminController::class, 'assignAdmin'])->name('assign.admin.submit');

        Route::get('/appraisal-master', function () {
            return view('appraisal_master'); 
        })->name('appraisal.view');

        Route::get('/getappraisaldata', [AppraisalMasterController::class, 'getAppraisalData'])->name('appraisaldata');
        Route::match(['get', 'post'], '/syncappraisalusers', [CommonController::class, 'syncAppraisalUsers'])->name('syncappraisalusers');

        Route::get('/getSyncedAppraisalUsers', [CommonController::class, 'getSyncedAppraisalUsers'])->name('getSyncedAppraisalUsers');
        Route::post('/storeAppraisalUsers', [CommonController::class, 'storeAppraisalUsers'])->name('storeAppraisalUsers');



    });



});

