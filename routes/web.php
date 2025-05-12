<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Carbon\Carbon;

Route::namespace('User')->middleware(['auth', 'user-access:sprovider'])->group(function(){
    Route::match(['GET', 'POST'],'/business_profile','UserController@business_profile')->name('business_profile'); 
});