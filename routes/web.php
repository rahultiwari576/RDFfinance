<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login/email', [LoginController::class, 'loginWithEmail'])->name('login.email')->middleware('guest');
Route::post('/login/aadhar', [LoginController::class, 'loginWithAadhar'])->name('login.aadhar')->middleware('guest');
Route::post('/login/otp', [LoginController::class, 'verifyOtp'])->name('login.otp')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register/extract-aadhar', [RegistrationController::class, 'extractAadhar'])->name('register.extract-aadhar')->middleware('guest');
Route::post('/register', [RegistrationController::class, 'register'])->name('register.submit')->middleware('guest');

Route::get('/emi-calculator', [LoanController::class, 'guestCalculator'])->name('emi.guest');
Route::post('/emi-calculator', [LoanController::class, 'calculate'])->name('emi.calculate');

Route::get('/home', HomeController::class)->name('home')->middleware('auth');

Route::prefix('loans')->middleware('auth')->group(function () {
    Route::post('/', [LoanController::class, 'apply'])->name('loans.apply');
    Route::get('/', [LoanController::class, 'list'])->name('loans.list');
    Route::get('{loan}/installments', [LoanController::class, 'installments'])->name('loans.installments');
    Route::post('installments/{installment}/pay', [LoanController::class, 'markInstallmentPaid'])->name('loans.installments.pay');
    Route::get('/reminders/list', [LoanController::class, 'reminders'])->name('loans.reminders');
});

