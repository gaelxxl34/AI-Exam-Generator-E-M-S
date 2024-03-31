<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterLecturerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadExamsController;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureLecturerRole;




Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/fst/fstmaster', function () {
    return view('fst.fstmaster');
})->name('fst.fstmaster');

Route::get('/fst/fstmaster-common-page', function () {
    return view('fst.fstmaster-common-page');
})->name('fst.fstmaster-common-page');



Route::get('/fst/fstbachelor', function () {
    return view('fst.fstbachelor');
})->name('fst.fstbachelor');

// 
Route::get('/fst/fstbachelor-common-page', function () {
    return view('fst.fstbachelor-common-page');
})->name('fst.fstbachelor-common-page');



Route::get('/fst/fstdiploma', function () {
    return view('fst.fstdiploma');
})->name('fst.fstdiploma');

// 

Route::get('/fst/fstdiploma-common-page', function () {
    return view('fst.fstdiploma-common-page');
})->name('fst.fstdiploma-common-page');



// Login and forget password
Route::post('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

Route::get('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');
Route::post('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');
Route::post('forget-password', [AuthController::class, 'sendPasswordResetLink'])->name('forget-password.action');
Route::get('/forget-password', [AuthController::class, 'showForgetPasswordForm'])->name('forget-password');




// -- admin
Route::post('/upload-lecturer', [RegisterLecturerController::class, 'registerLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('upload.lecturer');
Route::get('/admin/lecturer-list', [RegisterLecturerController::class, 'lecturerList'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.lecturer-list');
Route::get('/edit-lecturer/{id}', [RegisterLecturerController::class, 'editLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('editLecturer');
Route::put('/admin/update-lecturer-data/{lecturerId}', [RegisterLecturerController::class, 'updateLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.update-lecturer-data');
Route::delete('lecturer.delete/{lecturerId}', [RegisterLecturerController::class, 'deleteLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('lecturer.delete');



Route::post('/admin/view-generated-exam', [UploadExamsController::class, 'getRandomQuestions'])
    ->name('admin.view-generated-exam');
Route::post('/download-exam', [UploadExamsController::class, 'generatePdf'])
    ->name('download.exam');





Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(EnsureAdminRole::class)->name('admin.dashboard');


Route::get('/admin/add-lecturer', function () {
    return view('admin.add-lecturer');
})->middleware(EnsureAdminRole::class)
    ->name('admin.add-lecturer');
Route::get('/admin/add-past-exams', function () {
    return view('admin.add-past-exams');
})->middleware(EnsureAdminRole::class)
->name('admin.add-past-exams');
Route::get('/admin/ai-exam-generator', function () {
    return view('admin.ai-exam-generator');
})->middleware(EnsureAdminRole::class)
->name('admin.ai-exam-generator');




// -- Lecturer 

Route::post('/upload-exam', [UploadExamsController::class, 'uploadExam'])
     ->name('upload.exam');

Route::get('/lecturer/l-dashboard', function () {
    return view('lecturer.l-dashboard');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-dashboard');


Route::get('/lecturer/l-upload-questions', function () {
    return view('lecturer.l-upload-questions');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-upload-questions');