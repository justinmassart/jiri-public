<?php

use App\Livewire\Contacts\Contacts;
use App\Livewire\Evaluator\EvaluatorDashboard;
use App\Livewire\Evaluator\EvaluatorLogin;
use App\Livewire\Home;
use App\Livewire\Jiris\Jiris;
use App\Livewire\Live\LiveEvaluation;
use App\Livewire\LoginOrRegister;
use App\Livewire\Profile\ShowProfile;
use App\Livewire\Score\Scores;
use App\Livewire\Student\ShowProject;
use App\Livewire\Student\ShowStudent;
use Illuminate\Support\Facades\Route;

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

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginOrRegister::class)->middleware('guest')->name('login');
    Route::get('/evaluator/login/{token}', EvaluatorLogin::class)->name('evaluator.login');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/', Home::class)->name('dashboard');
    Route::get('/contacts', Contacts::class)->name('contacts');
    Route::get('/contacts/{contact?}', Contacts::class)->name('contact');
    Route::get('/scores', Scores::class)->name('scores');
    Route::get('/scores/{jiri?}', Scores::class)->name('score');
    Route::get('/jiris', Jiris::class)->name('jiris');
    Route::get('/jiris/{jiri?}', Jiris::class)->name('jiri');
    Route::get('/profile', ShowProfile::class)->name('profile');
    Route::get('/live/{jiri}', LiveEvaluation::class)->name('live');
});

Route::middleware('auth:contact')->group(function () {
    Route::get('/{jiri}', EvaluatorDashboard::class)->name('evaluator.dashboard');
    Route::get('/{jiri}/{student}', ShowStudent::class)->name('show.student');
    Route::get('/{jiri}/{student}/{jiri_project}', ShowProject::class)->name('show.project');
});
