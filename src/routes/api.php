<?php

use App\Infrastructure\Contact\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::apiResource('contacts', ContactController::class);
