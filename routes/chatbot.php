<?php

use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:chatbot')
    ->name('chatbot.message');
