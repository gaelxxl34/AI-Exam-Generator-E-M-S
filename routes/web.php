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

// Sitemap for SEO
Route::get('/sitemap.xml', function () {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $sitemap .= '<url>';
    $sitemap .= '<loc>' . url('/') . '</loc>';
    $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
    $sitemap .= '<changefreq>daily</changefreq>';
    $sitemap .= '<priority>1.0</priority>';
    $sitemap .= '</url>';
    
    // Login page
    $sitemap .= '<url>';
    $sitemap .= '<loc>' . url('/login') . '</loc>';
    $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
    $sitemap .= '<changefreq>monthly</changefreq>';
    $sitemap .= '<priority>0.9</priority>';
    $sitemap .= '</url>';
    
    // Faculty program pages
    $faculties = [
        ['faculty' => 'fst', 'name' => 'Faculty of Science and Technology'],
        ['faculty' => 'fbm', 'name' => 'Faculty of Business'],
        ['faculty' => 'foe', 'name' => 'Faculty of Engineering'],
        ['faculty' => 'fol', 'name' => 'Faculty of Law']
    ];
    
    $degrees = ['master', 'bachelor', 'diploma', 'hec'];
    
    foreach ($faculties as $faculty) {
        foreach ($degrees as $degree) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . route('exams.program', ['faculty' => $faculty['faculty'], 'degree' => $degree]) . '</loc>';
            $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap)->header('Content-Type', 'application/xml');
})->name('sitemap');

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



// Show the login form:
    Route::get('login', [AuthController::class, 'showLoginForm'])
    ->name('login');

// Handle the form POST:
Route::post('authenticate', [AuthController::class, 'authenticate'])
    ->name('authenticate');


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
    Route::get('/deans/dean-dashboard', [DashboardController::class, 'dashboardStats'])->name('dean.dashboard');
    Route::get('/deans/dean-moderation', [DashboardController::class, 'index'])->name('dean.moderation');
    Route::get('/deans/dashboard/report', [DashboardController::class, 'exportDashboardReport'])
    ->name('dashboard.export-report');
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


Route::get('/preview-pdf/{courseUnit}', [CourseController::class, 'previewPdf'])
    ->where('courseUnit', '.*')
    ->name('preview.pdf');



// Delete Question
Route::delete('/exams/{courseUnit}/{sectionName}/{questionIndex}/delete', [CourseController::class, 'deleteQuestion'])
    ->where('courseUnit', '.*')
    ->name('delete.question');

// Add Question
Route::post('/exams/{courseUnit}/questions/add', [CourseController::class, 'addQuestion'])
    ->where('courseUnit', '.*')
    ->name('add.question');

Route::get('/delete-past-exam/{id}', [PastExamController::class, 'delete'])->name('delete-past-exam');

///////
Route::get('/lecturer/l-dashboard', [CourseController::class, 'fetchCourses'])
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.l-dashboard');

Route::get('/lecturer/l-course-exams/{courseUnit}', [CourseController::class, 'courseDetails'])
    ->where('courseUnit', '.*')
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.l-course-exams');

//////////

Route::get('/lecturer/l-upload-questions', function () {
    return view('lecturer.l-upload-questions');
})->middleware(EnsureLecturerRole::class)->name('lecturer.l-upload-questions');

Route::get('/lecturer/lecturer.l-upload-questions', [CourseController::class, 'CoursesList'])
    ->middleware(EnsureLecturerRole::class)
    ->name('lecturer.list');





/// -- SUPER  ADMIN ROUTING

Route::get('/superadmin/super-adm-dashboard', function () {
    return view('superadmin.super-adm-dashboard');
})->middleware(EnsureSuperAdminRole::class)->name('superadmin.super-admin-dashboard');

Route::get('/superadmin/lecturer-control', [SuperAdminController::class, 'manageLecturers'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.lecturerControl');

Route::post('/superadmin/lecturer-control/toggle/{uid}', [SuperAdminController::class, 'toggleLecturerStatus'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.toggle-lecturer-status');

Route::post('/superadmin/lecturer-control/toggle-all', [SuperAdminController::class, 'toggleAllLecturersStatus'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.toggle-all-lecturers');

Route::post('/superadmin/lecturer-control/clear-courses/{uid}', [SuperAdminController::class, 'clearLecturerCourses'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.clear-lecturer-courses');

Route::post('/superadmin/lecturer-control/clear-all-courses', [SuperAdminController::class, 'clearAllLecturersCourses'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.clear-all-lecturer-courses');




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

// ✨ NEW: Unified dynamic route for all faculty programs
Route::get('/exams/{faculty}/{degree}', [PastExamController::class, 'fetchProgramExams'])
    ->name('exams.program')
    ->where(['faculty' => 'fst|fbm|foe|fol', 'degree' => 'master|bachelor|diploma|hec']);

// 🔄 LEGACY ROUTES: Keep for backward compatibility (redirects to new route)
Route::get('/fetch-mit-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fst', 'degree' => 'master']);
})->name('fst.fstmaster');

Route::get('/fetch-bachelor-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fst', 'degree' => 'bachelor']);
})->name('fst.fstbachelor');

Route::get('/fetch-dcs-diploma-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fst', 'degree' => 'diploma']);
})->name('fst.fstdiploma');

Route::get('/fetch-fbm-bachelor-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fbm', 'degree' => 'bachelor']);
})->name('fbm.bachelor');

Route::get('/fetch-diploma-business-public-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fbm', 'degree' => 'diploma']);
})->name('fetch.diploma.business.public.exams');

Route::get('/fetch-foe-bachelor-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'foe', 'degree' => 'bachelor']);
})->name('fetch.foe.bachelor.exams');

Route::get('/fetch-foe-diploma-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'foe', 'degree' => 'diploma']);
})->name('fetch.foe.diploma.exams');

Route::get('/fetch-law-bachelor-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fol', 'degree' => 'bachelor']);
})->name('fetch.law.bachelor.exams');

Route::get('/fetch-hec-exams', function() {
    return redirect()->route('exams.program', ['faculty' => 'fbm', 'degree' => 'hec']);
})->name('fetch.hec.exams');

// Lazy load PDF files on demand
Route::get('/fetch-pdf/{id}', [PastExamController::class, 'fetchPdfFile'])
    ->name('fetch.pdf');

Route::post('/superadmin/archive-exams', [SuperAdminController::class, 'startArchiveExams'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.archive-exams');
Route::get('/superadmin/archive-exams/progress/{jobId}', [SuperAdminController::class, 'archiveExamsProgress'])
    ->middleware(EnsureSuperAdminRole::class);

Route::post('/superadmin/delete-exams', [SuperAdminController::class, 'startDeleteExams'])
    ->middleware(EnsureSuperAdminRole::class)
    ->name('superadmin.delete-exams');
Route::get('/superadmin/delete-exams/progress/{jobId}', [SuperAdminController::class, 'deleteExamsProgress'])
    ->middleware(EnsureSuperAdminRole::class);