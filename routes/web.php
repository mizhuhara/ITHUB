<?php

use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminBannerController;
use App\Http\Controllers\AdminCommunityApprovalController;
use App\Http\Controllers\AdminCommunityController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminEventPaymentController;
use App\Http\Controllers\AdminInternshipController;
use App\Http\Controllers\AdminJobController;
use App\Http\Controllers\AdminMembershipController;
use App\Http\Controllers\AdminPromoPopupController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSchoolController;
use App\Http\Controllers\AdminUnivController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CommunityChatController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InstitutionMembershipController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\MyApplicationController;
use App\Http\Controllers\OnlineEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserReportController;
use App\Models\AppSetting;
use App\Models\Banner;
use App\Models\Event;
use App\Models\JobListing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestJobs = JobListing::with(['user.company'])
        ->where('is_active', true)
        ->where('status', '!=', 'Draft')
        ->latest('id')
        ->take(6)
        ->get();

    $upcomingEvents = Event::where('status', 'Approved')
        ->latest('id')
        ->take(3)
        ->get();

    Banner::syncActiveQueue();

    $banners = Banner::where('status', 'active')
        ->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
        ->orderBy('sort_order')
        ->orderByDesc('started_at')
        ->orderByDesc('id')
        ->take(5)
        ->get();

    $promoPopup = null;

    if (auth()->user()?->role !== 'super_admin' && ! request()->session()->get('promo_popup_seen')) {
        $promo = AppSetting::getPromoPopupSettings();

        if ($promo['enabled'] && $promo['title'] !== '') {
            $promoPopup = $promo;
            request()->session()->put('promo_popup_seen', true);
        }
    }

    return view('welcome', compact('latestJobs', 'upcomingEvents', 'banners', 'promoPopup'));
})->name('welcome');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/education', [EducationController::class, 'index'])->name('education.index');
Route::get('/education/{id}', [EducationController::class, 'show'])->name('education.show');

Route::get('/campus', [CampusController::class, 'index'])->name('campus.index');
Route::get('/campus/{id}', [CampusController::class, 'show'])->name('campus.show');

Route::get('/industry', [IndustryController::class, 'index'])->name('industry');
Route::post('/industry/company/{company}/view', [IndustryController::class, 'incrementView'])
    ->name('industry.company.view');

Route::get('/career', [CareerController::class, 'index'])->name('career.index');
Route::get('/career/kerja/daftar', [CareerController::class, 'createJobSeeker'])
    ->name('career.seeker.create');
Route::post('/career/kerja', [CareerController::class, 'storeJobSeeker'])
    ->name('career.seeker.store')
    ->middleware('throttle:5,1');
Route::get('/career/kerja/{id}', [CareerController::class, 'showJobSeeker'])
    ->whereNumber('id')
    ->name('career.seeker.show');
Route::get('/career/{type}/{id}/apply', [CareerController::class, 'apply'])
    ->whereIn('type', ['job', 'internship'])
    ->name('career.apply');
Route::post('/career/{type}/{id}/apply', [CareerController::class, 'storeApplication'])
    ->whereIn('type', ['job', 'internship'])
    ->name('career.apply.store')
    ->middleware('throttle:10,1');

Route::get('/events', [EventController::class, 'index'])->name('event.index');
Route::get('/events/join/{token}', [OnlineEventController::class, 'join'])
    ->middleware(['auth', 'verified', 'throttle:30,1'])
    ->name('event.join');
Route::get('/events/{id}', [EventController::class, 'show'])->name('event.show');
Route::post('/events/{event}/register', [EventController::class, 'register'])
    ->middleware(['auth', 'verified', 'throttle:10,1'])
    ->name('event.register');
Route::post('/events/{event}/reupload-proof', [EventController::class, 'reuploadProof'])
    ->middleware(['auth', 'verified', 'throttle:10,1'])
    ->name('event.reupload-proof');
Route::delete('/events/{event}/register', [EventController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'throttle:10,1'])
    ->name('event.unregister');
Route::get('/event', function () {
    return redirect()->route('event.index');
});
Route::get('/event/{id}', function ($id) {
    return redirect()->route('event.show', $id);
});
Route::get('/ticket/{ticket_code}', [TicketController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('ticket.show');
Route::post('/ticket/{ticket_code}/check-in', [TicketController::class, 'checkIn'])
    ->middleware(['auth', 'verified', 'throttle:60,1'])
    ->name('ticket.check-in');
Route::get('/ticket/{ticket_code}/certificate', [CertificateController::class, 'download'])
    ->middleware('throttle:30,1')
    ->name('ticket.certificate');

Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/create', [CommunityController::class, 'create'])->name('community.create');
Route::post('/community', [CommunityController::class, 'store'])->name('community.store')->middleware('throttle:15,1');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/community/my', [CommunityController::class, 'my'])->name('community.my');
    Route::get('/community/{community}/manage', [CommunityController::class, 'manage'])->name('community.manage');
    Route::put('/community/{community}/manage', [CommunityController::class, 'updateSettings'])->name('community.manage.update');
    Route::get('/community/{community}/members/{user}', [CommunityController::class, 'memberInfo'])->name('community.member.show');
    Route::patch('/community/{community}/members/{user}/role', [CommunityController::class, 'updateMemberRole'])->name('community.manage.member.role');
    Route::delete('/community/{community}/members/{user}/kick', [CommunityController::class, 'kickMember'])->name('community.manage.kick');
    Route::post('/community/{community}/members/{user}/toggle-ban', [CommunityController::class, 'toggleBanMember'])->name('community.manage.ban');
    Route::patch('/community/{community}/posts/{post}/moderate', [CommunityController::class, 'moderatePost'])->name('community.manage.post.moderate');
    Route::post('/community/{community}/posts', [CommunityController::class, 'storePost'])->name('community.posts.store')->middleware('throttle:20,1');
    Route::post('/community/{community}/resubmit', [CommunityController::class, 'resubmit'])->name('community.resubmit');

    // Community Chat Routes
    Route::get('/chat', [CommunityChatController::class, 'all'])->name('chat.index');
    Route::get('/community/chat/navbar-summary', [CommunityChatController::class, 'dropdownSummary'])->name('community.chat.navbar_summary');
    Route::post('/community/{community}/chat/read', [CommunityChatController::class, 'markAsRead'])->name('community.chat.mark_read');
    Route::get('/community/{community}/chat', [CommunityChatController::class, 'index'])->name('community.chat');
    Route::get('/community/{community}/chat/messages', [CommunityChatController::class, 'messages'])->name('community.chat.messages');
    Route::get('/community/{community}/chat/members', [CommunityChatController::class, 'membersList'])->name('community.chat.members');
    Route::post('/community/{community}/chat/send', [CommunityChatController::class, 'send'])->name('community.chat.send')->middleware('throttle:60,1');
    Route::post('/community/{community}/chat/upload-chunk', [CommunityChatController::class, 'uploadChunk'])->name('community.chat.upload_chunk')->middleware('throttle:120,1');
    Route::get('/community/{community}/chat/attachments/{attachment}/preview', [CommunityChatController::class, 'preview'])->name('community.chat.preview');
    Route::get('/community/{community}/chat/attachments/{attachment}/download', [CommunityChatController::class, 'download'])->name('community.chat.download');
    Route::get('/community/{community}/chat/messages/{message}/download-folder', [CommunityChatController::class, 'downloadFolder'])->name('community.chat.download_folder');
});

Route::get('/community/{community}', [CommunityController::class, 'show'])->name('community.show');
Route::post('/community/{community}/join', [CommunityController::class, 'join'])
    ->middleware(['auth', 'verified', 'throttle:10,1'])
    ->name('community.join');
Route::delete('/community/{community}/leave', [CommunityController::class, 'leave'])
    ->middleware(['auth', 'verified', 'throttle:10,1'])
    ->name('community.leave');

// Admin routes — semua butuh login + role admin
Route::middleware(['admin', 'verified', 'throttle:120,1'])->group(function () {

    // Admin Dashboard
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');

    // Admin Job Applications
    Route::get('/admin/applications', [AdminApplicationController::class, 'index'])->name('admin.applications.index');
    Route::patch('/admin/applications/{application}', [AdminApplicationController::class, 'update'])->name('admin.applications.update');
    Route::get('/admin/applications/{application}/resume', [AdminApplicationController::class, 'resume'])->name('admin.applications.resume');
    Route::delete('/admin/applications/{application}', [AdminApplicationController::class, 'destroy'])->name('admin.applications.destroy');

    // Admin School Management
    Route::get('/admin/schools', [AdminSchoolController::class, 'index'])->name('admin.schools.index');
    Route::get('/admin/schools/create', [AdminSchoolController::class, 'create'])->name('admin.schools.create');
    Route::post('/admin/schools', [AdminSchoolController::class, 'store'])->name('admin.schools.store');
    Route::post('/admin/editor-upload', [AdminSchoolController::class, 'editorUpload'])->name('admin.editor.upload');
    Route::get('/admin/schools/report-bug', [AdminSchoolController::class, 'reportBug'])->name('admin.school.report-bug');
    Route::post('/admin/schools/report-bug', [AdminSchoolController::class, 'storeReportBug'])->name('admin.school.report-bug.store');
    Route::get('/admin/schools/{id}', [AdminSchoolController::class, 'show'])->name('admin.schools.show');
    Route::get('/admin/schools/{id}/edit', [AdminSchoolController::class, 'edit'])->name('admin.schools.edit');
    Route::put('/admin/schools/{id}', [AdminSchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('/admin/schools/{id}', [AdminSchoolController::class, 'destroy'])->name('admin.schools.destroy');

    // Admin University Management
    Route::get('/admin/univ', [AdminUnivController::class, 'index'])->name('admin.univ.index');
    Route::get('/admin/univ/create', [AdminUnivController::class, 'create'])->name('admin.univ.create');
    Route::post('/admin/univ', [AdminUnivController::class, 'store'])->name('admin.univ.store');
    Route::get('/admin/univ/report-bug', [AdminUnivController::class, 'reportBug'])->name('admin.univ.report-bug');
    Route::post('/admin/univ/report-bug', [AdminUnivController::class, 'storeReportBug'])->name('admin.univ.report-bug.store');
    Route::get('/admin/univ/{id}', [AdminUnivController::class, 'show'])->name('admin.univ.show');
    Route::get('/admin/univ/{id}/edit', [AdminUnivController::class, 'edit'])->name('admin.univ.edit');
    Route::put('/admin/univ/{id}', [AdminUnivController::class, 'update'])->name('admin.univ.update');
    Route::delete('/admin/univ/{id}', [AdminUnivController::class, 'destroy'])->name('admin.univ.destroy');

    // Admin Company Management
    Route::get('/admin/company', [AdminCompanyController::class, 'index'])->name('admin.company.index');
    Route::get('/admin/company/create', [AdminCompanyController::class, 'create'])->name('admin.company.create');
    Route::post('/admin/company', [AdminCompanyController::class, 'store'])->name('admin.company.store');
    Route::get('/admin/company/report-bug', [AdminCompanyController::class, 'reportBug'])->name('admin.company.report-bug');
    Route::post('/admin/company/report-bug', [AdminCompanyController::class, 'storeReportBug'])->name('admin.company.report-bug.store');
    Route::get('/admin/company/{id}', [AdminCompanyController::class, 'show'])->name('admin.company.show');
    Route::get('/admin/company/{id}/edit', [AdminCompanyController::class, 'edit'])->name('admin.company.edit');
    Route::put('/admin/company/{id}', [AdminCompanyController::class, 'update'])->name('admin.company.update');
    Route::delete('/admin/company/{id}', [AdminCompanyController::class, 'destroy'])->name('admin.company.destroy');

    // Admin Community Management
    Route::get('/admin/community', [AdminCommunityController::class, 'index'])->name('admin.community.index');
    Route::get('/admin/community/create', [AdminCommunityController::class, 'create'])->name('admin.community.create');
    Route::post('/admin/community', [AdminCommunityController::class, 'store'])->name('admin.community.store');
    Route::get('/admin/community/{id}', [AdminCommunityController::class, 'show'])->name('admin.community.show');
    Route::get('/admin/community/{id}/edit', [AdminCommunityController::class, 'edit'])->name('admin.community.edit');
    Route::put('/admin/community/{id}', [AdminCommunityController::class, 'update'])->name('admin.community.update');
    Route::delete('/admin/community/{id}', [AdminCommunityController::class, 'destroy'])->name('admin.community.destroy');

    // Admin Community Approvals (Super Admin Only)
    Route::get('/admin/community-approvals', [AdminCommunityApprovalController::class, 'index'])->name('admin.community-approvals.index');
    Route::get('/admin/community-approvals/{community}', [AdminCommunityApprovalController::class, 'show'])->name('admin.community-approvals.show');
    Route::post('/admin/community-approvals/{community}/approve', [AdminCommunityApprovalController::class, 'approve'])->name('admin.community-approvals.approve');
    Route::post('/admin/community-approvals/{community}/reject', [AdminCommunityApprovalController::class, 'reject'])->name('admin.community-approvals.reject');

    // Admin Jobs Management
    Route::get('/admin/jobs', [AdminJobController::class, 'index'])->name('admin.jobs.index');
    Route::get('/admin/jobs/create', [AdminJobController::class, 'create'])->name('admin.jobs.create');
    Route::post('/admin/jobs', [AdminJobController::class, 'store'])->name('admin.jobs.store');
    Route::get('/admin/jobs/export', [AdminJobController::class, 'export'])->name('admin.jobs.export');
    Route::get('/admin/jobs/{id}', [AdminJobController::class, 'show'])->name('admin.jobs.show');
    Route::get('/admin/jobs/{job}/applications/{application}/resume', [AdminJobController::class, 'downloadResume'])->name('admin.jobs.applications.resume');
    Route::patch('/admin/jobs/{job}/applications/{application}/status', [AdminJobController::class, 'updateApplicationStatus'])->name('admin.jobs.applications.status');
    Route::get('/admin/jobs/{id}/edit', [AdminJobController::class, 'edit'])->name('admin.jobs.edit');
    Route::put('/admin/jobs/{id}', [AdminJobController::class, 'update'])->name('admin.jobs.update');
    Route::delete('/admin/jobs/{id}', [AdminJobController::class, 'destroy'])->name('admin.jobs.destroy');

    // Admin Internships Management
    Route::get('/admin/internships', [AdminInternshipController::class, 'index'])->name('admin.internships.index');
    Route::get('/admin/internships/create', [AdminInternshipController::class, 'create'])->name('admin.internships.create');
    Route::post('/admin/internships', [AdminInternshipController::class, 'store'])->name('admin.internships.store');
    Route::put('/admin/internships/{id}', [AdminInternshipController::class, 'update'])->name('admin.internships.update');
    Route::delete('/admin/internships/{id}', [AdminInternshipController::class, 'destroy'])->name('admin.internships.destroy');

    // Admin User & Verification Routes
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{id}/toggle-hide', [AdminUserController::class, 'toggleHide'])->name('admin.users.toggle-hide');

    Route::get('/admin/verification', [AdminVerificationController::class, 'index'])->name('admin.verification.index');
    Route::get('/admin/verification/{user}/document', [AdminVerificationController::class, 'document'])->name('admin.verification.document');
    Route::post('/admin/verification/{user}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verification.approve');
    Route::post('/admin/verification/{user}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verification.reject');
    Route::post('/admin/verification/{user}/toggle-hide', [AdminVerificationController::class, 'toggleHide'])->name('admin.verification.toggle-hide');
    Route::delete('/admin/verification/{user}', [AdminVerificationController::class, 'destroy'])->name('admin.verification.destroy');
    Route::get('/admin/user', function () {
        return redirect()->route('admin.users.index');
    });

    // Admin Event Verification & Management Routes
    Route::get('/admin/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::get('/admin/events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
    Route::post('/admin/events', [AdminEventController::class, 'store'])->name('admin.events.store');

    // Event Payment, Confirmations, Finance & Bank Settings (Must be before {id})
    Route::get('/admin/events/confirmations', [AdminEventPaymentController::class, 'confirmations'])->name('admin.events.confirmations');
    Route::post('/admin/events/registrations/{registration}/confirm', [AdminEventPaymentController::class, 'confirm'])->name('admin.events.registrations.confirm');
    Route::post('/admin/events/registrations/{registration}/reject', [AdminEventPaymentController::class, 'reject'])->name('admin.events.registrations.reject');
    Route::get('/admin/events/finance', [AdminEventPaymentController::class, 'finance'])->name('admin.events.finance');
    Route::get('/admin/events/bank-account', [AdminEventPaymentController::class, 'bankAccount'])->name('admin.events.bank-account');
    Route::post('/admin/events/bank-account', [AdminEventPaymentController::class, 'updateBankAccount'])->name('admin.events.bank-account.update');

    Route::get('/admin/events/{id}', [AdminEventController::class, 'show'])->name('admin.events.show');
    Route::get('/admin/events/{id}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/admin/events/{id}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::post('/admin/events/{event}/registrations/{registration}/check-in', [AdminEventController::class, 'toggleAttendance'])
        ->name('admin.events.registrations.check-in');
    Route::post('/admin/events/{id}/complete', [AdminEventController::class, 'complete'])->name('admin.events.complete');
    Route::post('/admin/events/{id}/approve', [AdminEventController::class, 'approve'])->name('admin.events.approve');
    Route::post('/admin/events/{id}/reject', [AdminEventController::class, 'reject'])->name('admin.events.reject');
    Route::delete('/admin/events/{id}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    // Admin Reports
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/{type}/export', [AdminReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/admin/reports/{type}/export/pdf', [AdminReportController::class, 'exportPdf'])->name('admin.reports.export.pdf');
    Route::get('/admin/reports/{type}/export/xlsx', [AdminReportController::class, 'exportXlsx'])->name('admin.reports.export.xlsx');
    Route::get('/admin/reports/{type}', [AdminReportController::class, 'show'])->name('admin.reports.show');
    Route::patch('/admin/user-reports/{userReport}', [AdminReportController::class, 'updateUserReport'])->name('admin.user-reports.update');

    // Admin Articles
    Route::get('/admin/articles', [AdminArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/admin/articles/create', [AdminArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/admin/articles', [AdminArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/admin/articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/admin/articles/{id}', [AdminArticleController::class, 'update'])->name('admin.articles.update');
    Route::post('/admin/articles/{id}/approve', [AdminArticleController::class, 'approve'])->name('admin.articles.approve');
    Route::post('/admin/articles/{id}/reject', [AdminArticleController::class, 'reject'])->name('admin.articles.reject');
    Route::delete('/admin/articles/{id}', [AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');

    // Admin Banners
    Route::get('/admin/banners', [AdminBannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/admin/banners/create', [AdminBannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/admin/banners', [AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::post('/admin/banners/{id}/promote', [AdminBannerController::class, 'promote'])->name('admin.banners.promote');
    Route::get('/admin/banners/{id}/edit', [AdminBannerController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/admin/banners/{id}', [AdminBannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/admin/banners/{id}', [AdminBannerController::class, 'destroy'])->name('admin.banners.destroy');

    Route::get('/admin/promo-popup', [AdminPromoPopupController::class, 'edit'])->name('admin.promo-popup.edit');
    Route::put('/admin/promo-popup', [AdminPromoPopupController::class, 'update'])->name('admin.promo-popup.update');

    // Admin Memberships & Premium Management (super_admin)
    Route::get('/admin/memberships', [AdminMembershipController::class, 'index'])->name('admin.memberships.index');
    Route::post('/admin/memberships/toggle-global', [AdminMembershipController::class, 'toggleGlobal'])->name('admin.memberships.toggle_global');
    Route::post('/admin/memberships/{user}/toggle', [AdminMembershipController::class, 'toggleUser'])->name('admin.memberships.toggle');
    Route::post('/admin/memberships/{user}/extend', [AdminMembershipController::class, 'update'])->name('admin.memberships.extend');
    Route::post('/admin/memberships/settings', [AdminMembershipController::class, 'updateSettings'])->name('admin.memberships.settings');
    Route::post('/admin/memberships/payments/{payment}/approve', [AdminMembershipController::class, 'approvePayment'])->name('admin.memberships.payments.approve');
    Route::post('/admin/memberships/payments/{payment}/reject', [AdminMembershipController::class, 'rejectPayment'])->name('admin.memberships.payments.reject');

}); // end admin group

// Role dashboards (semua user login)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/school', [DashboardController::class, 'school'])->name('dashboard.school');
    Route::get('/dashboard/university', [DashboardController::class, 'university'])->name('dashboard.university');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');
    Route::get('/dashboard/membership', [InstitutionMembershipController::class, 'index'])->name('dashboard.membership');
    Route::post('/dashboard/membership', [InstitutionMembershipController::class, 'store'])->name('dashboard.membership.store');
    Route::get('/dashboard/user', [DashboardController::class, 'user'])->name('dashboard.user');
    Route::get('/dashboard/applications', [MyApplicationController::class, 'index'])->name('applications.index');
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/dashboard/password', [ProfileController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('profile.password');
    Route::post('/dashboard/report', [UserReportController::class, 'store'])->name('user.report.store');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.submit');
Route::get('/register/success/{user_id?}', [AuthController::class, 'registerSuccess'])->name('register.success');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.store');
});

// Knowledge Hub Routes
Route::get('/knowledge-hub', [KnowledgeController::class, 'index'])->name('knowledge.index');
Route::get('/knowledge-hub/ai', [KnowledgeController::class, 'ai'])->name('knowledge.ai');
Route::get('/knowledge-hub/ai/{id}', [KnowledgeController::class, 'aiDetail'])->name('knowledge.ai.detail');
Route::get('/knowledge-hub/article/{id}', [KnowledgeController::class, 'show'])->name('knowledge.show');

// Support /knowladge legacy/typo URL as alias
Route::get('/knowladge', function () {
    return redirect()->route('knowledge.index');
});
Route::get('/knowladge/ai', function () {
    return redirect()->route('knowledge.ai');
});
Route::get('/knowladge/ai/{id}', function ($id) {
    return redirect()->route('knowledge.ai.detail', $id);
});
Route::get('/knowladge/article/{id}', function ($id) {
    return redirect()->route('knowledge.show', $id);
});
