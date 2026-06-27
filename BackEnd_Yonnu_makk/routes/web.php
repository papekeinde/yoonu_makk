<?php

use Illuminate\Support\Facades\Route;

Route::view('/assistant-ia', 'assistant')->name('assistant');
Route::view('/agenda',       'agenda')->name('agenda');

Route::get('/', function () {
    $frontendUrl = env('FRONTEND_URL');
    $frontendUrl = filter_var($frontendUrl, FILTER_VALIDATE_URL) ? $frontendUrl : null;

    $chatbotUrl = env('FRONTEND_CHATBOT_URL');
    $chatbotUrl = filter_var($chatbotUrl, FILTER_VALIDATE_URL)
        ? $chatbotUrl
        : route('assistant');

    return view('welcome', [
        'frontendUrl' => $frontendUrl,
        'chatbotUrl' => $chatbotUrl,
    ]);
});
