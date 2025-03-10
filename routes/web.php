<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterLecturerController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadExamsController;
use App\Http\Middleware\EnsureSuperAdminRole;
use App\Http\Middleware\EnsureGenAdminRole;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureLecturerRole;
use App\Http\Middleware\EnsureDeanRole;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PastExamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageUploadController;
//



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/fst/fstmaster-common-page', function () {
    return view('fst.fstmaster-common-page');
})->name('fst.fstmaster-common-page');




// 
Route::get('/fst/fstbachelor-common-page', function () {
    return view('fst.fstbachelor-common-page');
})->name('fst.fstbachelor-common-page');




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




// -- GENERAL ADMIN 

Route::get('/genadmin/gen-dashboard', [DashboardController::class, 'genAdminDashboard'])
    ->middleware(EnsureGenAdminRole::class)
    ->name('genadmin.gen-dashboard');
Route::get('/genadmin/ai-exam-generator', [CourseController::class, 'AllCourses'])
    ->name('genadmin.ai-exam-generator');

// DEAN ROUTING


Route::middleware([EnsureDeanRole::class])->group(function () {
    Route::get('/deans/dean-dashboard', [DashboardController::class, 'index'])->name('dean.dashboard');
});

Route::post('/course/update-status/{id}', [DashboardController::class, 'updateStatus'])->name('course.updateStatus');
Route::post('/deans/course/{id}/approve', [DashboardController::class, 'approve'])->name('course.approve');
Route::post('/deans/course/{id}/decline', [DashboardController::class, 'decline'])->name('course.decline');
// -- END OF DEAN ROUTING


// -- ADMIN ROUTING 

Route::put('/admin/update-lecturer-data/{lecturerId}', [RegisterLecturerController::class, 'updateLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.update-lecturer-data');



Route::post('/genadmin/view-generated-exam', [UploadExamsController::class, 'getRandomQuestions'])
    ->name('genadmin.view-generated-exam');
Route::post('/download-exam', [UploadExamsController::class, 'generatePdf'])
    ->name('download.exam');



Route::get('/admin/dashboard', [DashboardController::class,'adminDashboard'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.dashboard');


Route::get('/admin/add-courses', function () {
    return view('admin.add-courses');
})->middleware(EnsureAdminRole::class)->name('admin.add-courses');

Route::get('/admin/add-lecturer', function () {
    return view('admin.add-lecturer');
})->middleware(EnsureAdminRole::class)
    ->name('admin.add-lecturer');

Route::post('/upload-lecturer', [RegisterLecturerController::class, 'registerLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('upload.lecturer');

Route::get('/admin/lecturer-list', [RegisterLecturerController::class, 'lecturerList'])
    ->middleware(EnsureAdminRole::class)
    ->name('admin.lecturer-list');

Route::get('/edit-lecturer/{id}', [RegisterLecturerController::class, 'editLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('editLecturer');

Route::delete('lecturer.delete/{lecturerId}', [RegisterLecturerController::class, 'deleteLecturer'])
    ->middleware(EnsureAdminRole::class)
    ->name('lecturer.delete');


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




/// --- START OF COURSES BY FACULTY LIST

Route::post('/upload-courses', [CourseController::class, 'uploadCourses'])
    ->name('upload.courses');


Route::get('/admin/courses-list', [CourseController::class, 'showCourses'])
    ->name('admin.courses-list');



Route::get('/admin/edit-courses/{id}', [CourseController::class, 'editCourse'])->name('edit.course');

Route::put('/admin/edit-courses/{id}', [CourseController::class, 'updateCourse'])->name('update.course');

Route::delete('/admin/edit-courses/{id}', [CourseController::class, 'deleteCourse'])->name('course.delete');

/// -- END OF COURSES BY FACULTY LIST 





// -- Lecturer routing

Route::post('/upload-exam', [UploadExamsController::class, 'uploadExam'])
     ->name('upload.exam');

Route::get('/lecturer/l-dashboard', function () {
    return view('lecturer.l-dashboard');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-dashboard');

Route::put('/exams/{courseUnit}/{sectionName}/{questionIndex}/update', [CourseController::class, 'updateQuestion'])
->name('update.question');

// Route to upload an image to Firebase
Route::post('/upload-image', [ImageUploadController::class, 'uploadImage'])->name('upload.image');

// Route to delete unused images from Firebase
Route::post('/delete-unused-images', [ImageUploadController::class, 'deleteImages'])->name('delete.image');


Route::put('/exams/{courseUnit}/update-instructions', [CourseController::class, 'updateInstruction'])
    ->name('update.instructions');

// Route for uploading files
Route::post('/exams/{courseUnit}/file/upload', [CourseController::class, 'uploadFile'])->name('upload.file');

// Route for downloading the marking guide
Route::get('/exams/{courseUnit}/marking-guide/download', [CourseController::class, 'downloadMarkingGuide'])->name('download.markingGuide');


Route::get('/preview-pdf/{courseUnit}', [CourseController::class, 'previewPdf'])->name('preview.pdf');



Route::delete('/exams/{courseUnit}/{sectionName}/{questionIndex}/delete', [CourseController::class, 'deleteQuestion'])
->name('delete.question');

Route::post('/exams/{courseUnit}/questions/add', [CourseController::class, 'addQuestion'])
->name('add.question');

Route::get('/delete-past-exam/{id}', [PastExamController::class, 'delete'])->name('delete-past-exam');

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
})->middleware(EnsureSuperAdminRole::class)->name('superadmin.super-admin-dashboard');

Route::get('/superadmin/lecturer-control', [SuperAdminController::class, 'manageLecturers'])->name('superadmin.lecturerControl');
Route::post('/superadmin/toggle-lecturer/{uid}/{status}', [SuperAdminController::class, 'toggleLecturerStatus'])
    ->name('superadmin.toggleLecturerStatus');




Route::get('/superadmin/add-admin', function () {
    return view('superadmin.add-admin');
})->middleware(EnsureSuperAdminRole::class)->name('superadmin.add-admin');

Route::post('/upload-admin', [SuperAdminController::class, 'registerAdmins'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('upload.admin');

Route::get('/superadmin/admin-list', [SuperAdminController::class, 'adminsList'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.admin-list');

Route::get('/superadmin/edit-admin/{id}', [SuperAdminController::class, 'editAdmin'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('editAdmin');

Route::put('/update-admin/{adminId}', [SuperAdminController::class, 'updateAdminData'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('admin.update-admin-data');

Route::delete('/admin/{adminId}', [SuperAdminController::class, 'deleteAdmin'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('admin.delete');



    
// -- USERS NORMAL ROUTINGS 

Route::get('/fetch-mit-exams', [PastExamController::class, 'fetchMITExams'])
    ->name('fst.fstmaster');

Route::get('/fetch-bachelor-exams', [PastExamController::class, 'fetchBachelorExams'])
    ->name('fst.fstbachelor');

Route::get('/fetch-dcs-diploma-exams', [PastExamController::class, 'fetchDiplomaDCSExams'])
    ->name('fst.fstdiploma');

Route::get('/fetch-fbm-bachelor-exams', [PastExamController::class, 'fetchFBMBachelorExams'])
    ->name('fbm.bachelor');

Route::get('/fetch-diploma-business-public-exams', [PastExamController::class, 'fetchDiplomaBusinessAndPublicExams'])
    ->name('fetch.diploma.business.public.exams');

Route::get('/fetch-foe-bachelor-exams', [PastExamController::class, 'fetchFOEBachelorExams'])
    ->name('fetch.foe.bachelor.exams');

Route::get('/fetch-foe-diploma-exams', [PastExamController::class, 'fetchFOEDiplomaExams'])
    ->name('fetch.foe.diploma.exams');

Route::get('/fetch-law-bachelor-exams', [PastExamController::class, 'fetchLawBachelorExams'])
    ->name('fetch.law.bachelor.exams');

Route::get('/fetch-hec-exams', [PastExamController::class, 'fetchHECExams'])
    ->name('fetch.hec.exams');