<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'telegram:cryptognal_bot'])->name('cryptognal_bot.')->group(function () {
    Route::get('/page', function(){
        return view('cryptognal_bot::page');
    })->name('page');
});

