<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterLecturerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadExamsController;
use App\Http\Middleware\EnsureSuperAdminRole;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureLecturerRole;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PastExamController;
//



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




// -- ADMIN ROUTING 

Route::put('/admin/update-lecturer-data/{lecturerId}', [RegisterLecturerController::class, 'updateLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.update-lecturer-data');



Route::post('/admin/view-generated-exam', [UploadExamsController::class, 'getRandomQuestions'])
    ->name('admin.view-generated-exam');
Route::post('/download-exam', [UploadExamsController::class, 'generatePdf'])
    ->name('download.exam');


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(EnsureAdminRole::class)->name('admin.dashboard');

Route::get('/admin/add-courses', function () {
    return view('admin.add-courses');
})->middleware(EnsureAdminRole::class)->name('admin.add-courses');



// --- PAST EXAMS ROUTING START 
Route::get('/admin/add-past-exams', function () {
    return view('admin.add-past-exams');
})->middleware(EnsureAdminRole::class)
    ->name('admin.add-past-exams');

Route::post('/upload-past-exam',[PastExamController::class, 'store']);

Route::get('/admin.view-past-exams', [PastExamController::class, 'fetchPastExams'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.view-past-exams');

// PAST EXAMS ROUTING END 


Route::get('/admin/ai-exam-generator', function () {
    return view('admin.ai-exam-generator');
})->middleware(EnsureAdminRole::class)
->name('admin.ai-exam-generator');

/// --- START OF COURSES BY FACULTY LIST

Route::post('/upload-courses', [CourseController::class, 'uploadCourses'])
    ->name('upload.courses');


Route::get('/admin/courses-list', [CourseController::class, 'showCourses'])
    ->name('admin.courses-list');



Route::get('/admin/edit-courses/{id}', [CourseController::class, 'editCourse'])->name('edit.course');

Route::put('/admin/edit-courses/{id}', [CourseController::class, 'updateCourse'])->name('update.course');

/// -- END OF COURSES BY FACULTY LIST 





// -- Lecturer 

Route::post('/upload-exam', [UploadExamsController::class, 'uploadExam'])
     ->name('upload.exam');

Route::get('/lecturer/l-dashboard', function () {
    return view('lecturer.l-dashboard');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-dashboard');



///////



Route::get('/lecturer/l-dashboard', [CourseController::class, 'fetchCourses'])
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.l-dashboard');

Route::get('/lecturer/l-course-exams/{courseUnit}', [CourseController::class, 'courseDetails'])
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.l-course-exams');
//////////

Route::get('/lecturer/l-upload-questions', function () {
    return view('lecturer.l-upload-questions');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-upload-questions');

Route::get('/lecturer/lecturer.l-upload-questions', [CourseController::class, 'CoursesList'])
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.l-upload-questions');






/// -- SUPER  ADMIN ROUTING

Route::get('/superadmin/super-adm-dashboard', function () {
    return view('superadmin.super-adm-dashboard');
})->middleware(EnsureSuperAdminRole::class)->name('superadmin.super-adm-dashboard');

Route::get('/superadmin/add-lecturer', function () {
    return view('superadmin.add-lecturer');
})->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.add-lecturer');

Route::post('/upload-lecturer', [RegisterLecturerController::class, 'registerLecturer'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('upload.lecturer');

Route::get('/superadmin/lecturer-list', [RegisterLecturerController::class, 'lecturerList'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.lecturer-list');

Route::get('/edit-lecturer/{id}', [RegisterLecturerController::class, 'editLecturer'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('editLecturer');

Route::delete('lecturer.delete/{lecturerId}', [RegisterLecturerController::class, 'deleteLecturer'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('lecturer.delete');

Route::get('/superadmin/add-lecturer', [CourseController::class, 'AllCourses'])
    ->name('superadmin.add-lecturer');



