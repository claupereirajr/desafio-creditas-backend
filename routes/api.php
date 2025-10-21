<?php

use App\Http\Controllers\LoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('loan', [LoanController::class, 'loan']);
