<?php
use App\Http\Controllers\CommonController;
use App\Http\Controllers\AzureAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AttributeReviewController;

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

Route::match(['get', 'post'], '/syncappraisalusers', [CommonController::class, 'syncAppraisalUsers'])->name('syncappraisalusers');



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
});

