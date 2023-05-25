<?php

use App\Http\Controllers\GroceryController;
use App\Http\Controllers\ShowDocsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('/upload',[UploadController::class, 'upload'])->name('upload');

Route::get('/showDocs',[ShowDocsController::class, 'showDocs'])->name('showDocs');

Route::any('/document/{document}/parseToFile',[ShowDocsController::class, 'parseToFile'])->name('parseToFile');

Route::any('/document/{document}/parseToDisplay',[ShowDocsController::class, 'parseToDisplay'])->name('parseToDisplay');

Route::any('/document/exceptional/',[ShowDocsController::class, 'addExceptional'])->name('addExceptional');

Route::view('/grocery', 'grocery');
Route::post('/grocery/post', [GroceryController::class, 'store']);

Route::get('users', [UserController::class, 'index']);

Route::get('/showVerbs/{document}',[ShowDocsController::class, 'showVerbs'])->name('showVerbs');

