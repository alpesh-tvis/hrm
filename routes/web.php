<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UpworkAPIController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\AccountNameController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DailyworkReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveSettingController;
use App\Http\Controllers\ShiftSettingsController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ExtraDaysController;
use App\Http\Controllers\SalaryDeductionController;
use App\Http\Controllers\MailRequestController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\SettingController;
use App\Events\TestNotification;

use Illuminate\Foundation\Auth\EmailVerificationRequest;


// Route::get('/test-pusher', function () {
//     event(new TestNotification('Hello! Pusher test message.'));
//     return 'Event fired!';
// });

Route::get('/run-composer-update', function () {
    $output = shell_exec('composer update 2>&1');
    return "<pre>$output</pre>";
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');

    return "Cache and View cleared!";
});

Route::get('/', function () {
    //event(new TestNotification('Hello! Pusher test message.'));
    
    // return 'Event fired!';
    return redirect()->route('dashboard.index');
});

Auth::routes(['register'=>'false','login'=>'false']);

Route::middleware(['auth', 'verified'])->group(function () {

    // ----------------------------- Dashboard -----------------------------
    Route::resource('dashboard', DashboardController::class);

    // ----------------------------- Logout -----------------------------
    Route::any('logout', [EmployeeController::class,'logout'])->name('logout');

    // ----------------------------- Work Reports -----------------------------
    Route::resource('work_reports', ReportController::class);

    // ----------------------------- Holiday -----------------------------
    Route::get('/holiday', [HolidayController::class, 'index'])->name('holiday.index');

    // ----------------------------- Admin -----------------------------
    Route::resource('admin', EmployeeController::class);

    // ----------------------------- Client -----------------------------
    Route::resource('client', ClientController::class);

    // ----------------------------- Extra Days -----------------------------
    Route::resource('extra-days', ExtraDaysController::class);
    Route::get('/extra-days/{id}', [ExtraDaysController::class, 'show'])->name('extra_days_show');
    Route::get('/extra-days/edit/{id}', [ExtraDaysController::class, 'edit'])->name('extra_days_edit');
    Route::post('/extra-days/update/{id}', [ExtraDaysController::class, 'update'])->name('extra_days_update');
    Route::delete('/extra-days/destroy/{id}', [ExtraDaysController::class, 'destroy'])->name('extra_days_destroy');

    // ----------------------------- Salary Deduction -----------------------------
    Route::resource('salary-deduction', SalaryDeductionController::class); 
    Route::get('salary-deducted', [SalaryDeductionController::class, 'salary_deduction'])->name('salary-deducted');
    Route::get('leave-adjust', [SalaryDeductionController::class, 'leave_adjust'])->name('leave-adjust');
    Route::get('salary-deduction/edit/{id}', [SalaryDeductionController::class, 'edit'])->name('salary-deduction-edit');
    Route::post('salary-deduction/update/{id}', [SalaryDeductionController::class, 'update'])->name('salary-deduction-update');
    Route::delete('salary-deduction/destroy/{id}', [SalaryDeductionController::class, 'destroy'])->name('salary-deduction-destroy');
    Route::post('/update-leave-deduction', [SalaryDeductionController::class, 'updateLeaveDeduction']);

    // ----------------------------- Leave -----------------------------
    Route::get('/leave', [EmployeeController::class, 'leave_add'])->name('leave');
    Route::post('/leave', [EmployeeController::class, 'leave_post'])->name('leave');

    // ----------------------------- Leave Setting -----------------------------
    Route::get('/leave_setting', [LeaveSettingController::class, 'leave_setting'])->name('leave_setting');
    Route::get('leave_add_setting', [LeaveSettingController::class, 'leave_create'])->name('leave_add_setting');
    Route::post('add_setting', [LeaveSettingController::class, 'store'])->name('add_setting');
    Route::get('/leave_setting/{id}', [LeaveSettingController::class, 'settings_show'])->name('leave_show_setting');
    Route::get('leave_edit_setting/{id}', [LeaveSettingController::class, 'leave_edit'])->name('leave_edit_setting');
    Route::post('leave_update_setting/{id}', [LeaveSettingController::class, 'setting_update'])->name('leave_update_setting');
    Route::delete('leave_setting/{id}', [LeaveSettingController::class, 'setting_destroy'])->name('leave_setting.delete');
    Route::get('leave_update_setting', [LeaveSettingController::class, 'main_setting_edit_come_new_member'])->name('leave_updates_setting');
    Route::post('leave_update_settings', [LeaveSettingController::class, 'main_setting_update_come_new_memeber'])->name('leave_update_settings');

    // ----------------------------- Invoice -----------------------------
    Route::get('add-invoice', [StatementController::class, 'add_invoice'])->name('add_invoice');
    Route::post('add-invoice', [StatementController::class, 'storeInvoice'])->name('store_invoice');

    // ----------------------------- Transaction Type -----------------------------
    Route::resource('transaction-type', TransactionTypeController::class);

    // ----------------------------- Statement -----------------------------
    Route::resource('importStatement', StatementController::class);
    Route::get('/download', [StatementController::class, 'download'])->name('download');
    Route::get('/statement_sample', [StatementController::class, 'statement_sample'])->name('statement_sample');
    Route::post('/multi_select', [StatementController::class, 'multi_select'])->name('multi_select');
    Route::get('/print', [StatementController::class, 'print'])->name('print');
    Route::post('/generate_pdf', [StatementController::class, 'generate_pdf'])->name('generate_pdf');
    Route::get('/preview_pdf', [StatementController::class, 'preview_pdf'])->name('preview_pdf');
    Route::get('/zip_download', [StatementController::class, 'zip_download'])->name('zip_download');
    Route::get('/delete_w_rate/{id}', [StatementController::class, 'delete_w_rate'])->name('delete_w_rate');
    Route::get('ajax_statement', [StatementController::class, 'ajax_statement'])->name('ajax_statement');

    // ----------------------------- Rates -----------------------------
    Route::resource('rates', RateController::class);
    Route::get('/rate_sample', [RateController::class, 'rate_sample'])->name('rate_sample');
    Route::post('/add_rate', [RateController::class, 'add_rate'])->name('add_rate');

    // ----------------------------- Account Name -----------------------------
    Route::resource('accountname', AccountNameController::class);

    // ----------------------------- Password -----------------------------
    Route::get('/changePassword', [App\Http\Controllers\EmployeeController::class, 'showChangePasswordGet'])->name('changePasswordGet');
    Route::post('/changePassword', [App\Http\Controllers\EmployeeController::class, 'changePasswordPost'])->name('changePasswordPost');

    // ----------------------------- Admin Change Password -----------------------------
    Route::get('/admin_upchange', [App\Http\Controllers\EmployeeController::class, 'admin_upchange'])->name('admin_upchange');
    Route::post('/admin_upchange_post', [App\Http\Controllers\EmployeeController::class, 'admin_upchange_post'])->name('admin_upchange_post');

    // ----------------------------- Project -----------------------------
    Route::get('complete_project', [App\Http\Controllers\ProjectController::class, 'project_complete'])->name('project_complete');
    Route::get('project_complete_edit/{id}', [App\Http\Controllers\ProjectController::class, 'project_complete_edit'])->name('complete_project.project_complete_edit');
    Route::resource('project', ProjectController::class);
    Route::get('download_putty/{path}', [ProjectController::class, 'download_putty'])->name('download_putty');

    // ----------------------------- Bids -----------------------------
    Route::resource('bids', LeadController::class);

    // ----------------------------- Profile -----------------------------
    Route::resource('profile', ProfileController::class);
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');

    // ----------------------------- Daily Work Report -----------------------------
    Route::resource('daily_work_report', DailyworkReportController::class);
    Route::get('work_report', [DailyworkReportController::class, 'work_report'])->name('work_report');
    Route::post('/stop-timer', [WorkReportController::class, 'stopTimer'])->name('timer.stop');

    // ----------------------------- Leave Details -----------------------------
    Route::get('leave-details', [LeaveController::class, 'leave_details'])->name('leave_details');
    Route::resource('leave', LeaveController::class);

    // ----------------------------- Shift Settings -----------------------------
    Route::resource('shift-settings', ShiftSettingsController::class);

    // ----------------------------- Attendance Reports -----------------------------
    Route::get('late-coming', [ReportController::class, 'late_coming'])->name('late_coming');
    Route::get('first-shift', [ReportController::class, 'first_shift'])->name('first_shift');
    Route::get('second-shift', [ReportController::class, 'second_shift'])->name('second_shift');
    Route::get('early-going', [ReportController::class, 'early_going'])->name('early_going');

    // ----------------------------- Mail Request -----------------------------
    Route::resource('mail-request', MailRequestController::class);
    Route::get('request-mail', [MailRequestController::class, 'create'])->name('request-mail');

    // ----------------------------- Currency -----------------------------
    Route::resource('currency', CurrencyController::class);

    // ----------------------------- Settings -----------------------------
    Route::resource('setting', SettingController::class);

    // ----------------------------- Holiday CRUD Routes -----------------------------
    Route::prefix('holiday')->name('holiday.')->controller(HolidayController::class)->group(function () {
        Route::get('/create', 'create')->name('add');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });

});
   


/**
    * Email Verification Routes
*/
Auth::routes(['verify' => true]);

Route::get('/email/verify', function () {
    if (Auth::user() && ! Auth::user()->email_verified_at) {
        return view('auth.verify-email');
    }
    else{
        return redirect()->route('admin.index');
    }    
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/admin');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/mail-test', function () {
    try {
        Mail::raw('This is a Laravel SMTP test email.', function ($message) {
            $message->to('dev.tvistech@gmail.com')
                    ->subject('Laravel Mail Test');
        });

        \Log::info('Mail send completed');

        return 'Mail send completed.';
    } catch (\Exception $e) {
        \Log::error($e->getMessage());

        return $e->getMessage();
    }
});

Route::get('/dns-test', function () {
    return gethostbyname('mail.tvistech.com');
});

Route::get('/port-test', function () {

    $ports = [465, 587, 25];

    foreach ($ports as $port) {

        $fp = @fsockopen("mail.tvistech.com", $port, $errno, $errstr, 5);

        if ($fp) {
            fclose($fp);
            echo "Port {$port} : OPEN<br>";
        } else {
            echo "Port {$port} : CLOSED ({$errstr})<br>";
        }
    }
});

Route::get('/mail-check', function () {
    return [
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'encryption' => config('mail.mailers.smtp.encryption'),
        'username' => config('mail.mailers.smtp.username'),
        'from' => config('mail.from.address'),
    ];
});

require __DIR__.'/auth.php';