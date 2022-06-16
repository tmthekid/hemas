<?php

use App\Models\Result;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

Route::get('/', [AppController::class, 'index'])->name('home');
Route::post('client', [AppController::class, 'saveClient'])->name('post.client');
Route::get('otp', [AppController::class, 'getOTP'])->name('get.otp');
Route::post('otp', [AppController::class, 'verifyOTP'])->name('post.otp');
Route::post('resend', [AppController::class, 'resendOTP'])->name('post.resend.otp');
Route::get('wheel', [AppController::class, 'getWheel'])->name('get.wheel');
Route::post('wheel', [AppController::class, 'saveWheel'])->name('post.wheel');
Route::get('coupons', [AppController::class, 'getCoupons'])->name('get.coupons');
Route::get('code', [AppController::class, 'getCode'])->name('get.code');
Route::get('pdf', [AppController::class, 'downloadPDF'])->name('dowload.pdf');
Route::get('results', [AppController::class, 'getResults'])->name('get.results');