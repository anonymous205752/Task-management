<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    Mail::raw('This is a test email.', function ($message) {
        $message->to('olajidemicheal580@gmail.com')
                ->subject('Testing Laravel Mail');
    });

    return 'Email sent!';
});


Route::get('/', function () {
    return view('welcome');
});
