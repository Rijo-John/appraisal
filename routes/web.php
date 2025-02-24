<?php
use App\Http\Controllers\CommonController;
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


Route::get('/questions', [QuestionController::class, 'index']);
Route::post('/save-designation-questions', [QuestionController::class, 'saveDesignationQuestions']);

Route::get('/set-attribute-review', [AttributeReviewController::class, 'index']);
Route::post('/saveRatings', [AttributeReviewController::class, 'saveRatings']);