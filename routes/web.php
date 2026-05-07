<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;

// Show email debugger form
Route::get('/', [EmailController::class, 'index'])->name('index');

// Analyze email via SMTP and AI
Route::post('/analyze-email', [EmailController::class, 'analyzeEmail'])->name('analyze-email');

// Test SMTP connection without sending email
Route::post('/test-smtp', [EmailController::class, 'testSmtp'])->name('test-smtp');

// Check email authentication (SPF/DKIM/DMARC/MX)
Route::post('/check-auth', [EmailController::class, 'checkAuth'])->name('check-auth');
