<?php

use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/projects/{caseStudy}', [CaseStudyController::class, 'show'])->name('projects.show');

// The section was renamed from "Case studies"; links already shared keep working.
Route::permanentRedirect('/case-studies/{slug}', '/projects/{slug}');
