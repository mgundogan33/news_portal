<?php

use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


/*Admin*/
Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin.home');
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
Route::get('/admin/forget_password', [AdminLoginController::class, 'forget_password'])->name('admin.forget_password');
