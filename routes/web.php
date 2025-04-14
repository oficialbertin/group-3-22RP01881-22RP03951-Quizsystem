<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\AnswerController;
use App\Models\User;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        $user = User::with(['quizzes', 'attempts'])->find(auth()->id());
        return view('welcome', ['user' => $user]);
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Quiz routes
    Route::resource('quizzes', QuizController::class);
    Route::post('quizzes/{quiz}/publish', [QuizController::class, 'publish'])->name('quizzes.publish');
    Route::post('quizzes/{quiz}/unpublish', [QuizController::class, 'unpublish'])->name('quizzes.unpublish');
    
    // Questions routes
    Route::resource('quizzes.questions', QuestionController::class);
    
    // Options routes
    Route::resource('questions.options', OptionController::class);
    
    // Quiz attempts routes
    Route::resource('quiz-attempts', QuizAttemptController::class);
    
    // Answers routes
    Route::resource('answers', AnswerController::class);
    Route::post('answers/{answer}/grade', [AnswerController::class, 'grade'])->name('answers.grade');
    
    // Results routes
    Route::resource('results', ResultsController::class)->only(['index', 'show']);
});
