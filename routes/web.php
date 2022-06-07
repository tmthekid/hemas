<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppController::class, 'index'])->name('home');
Route::post('client', [AppController::class, 'saveClient'])->name('post.client');
Route::post('otp', [AppController::class, 'verifyOTP'])->name('post.otp');
Route::get('wheel', [AppController::class, 'getWheel'])->name('get.wheel');
Route::post('wheel', [AppController::class, 'saveWheel'])->name('post.wheel');
Route::get('code', [AppController::class, 'getCode'])->name('get.code');
Route::get('pdf', [AppController::class, 'downloadPDF'])->name('dowload.pdf');