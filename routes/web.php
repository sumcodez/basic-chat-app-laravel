<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home.home');
})->name('home.page');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/allUsers', [UserController::class, 'fetch_allUsers'])->name('users.all');

    Route::get('/allUsers/manageProfile', [UserController::class, 'manageProfile'])->name('users.manage_Profile');
    Route::post('/allUsers/update', [UserController::class, 'updateProfile'])->name('users.update_profile');
    Route::post('/allUsers/pic_update', [UserController::class, 'update_profile_pic'])->name('profile.update_pic');

    Route::get('/search_users', [UserController::class, 'searchUsers'])->name('users.search');

    Route::get('/connect/send/{receiver_id}', [ConnectionController::class, 'send'])->name('connect.send');
    Route::post('/connect/accept/{connection_id}', [ConnectionController::class, 'accept'])->name('connect.accept');
    Route::post('/connect/decline/{connection_id}', [ConnectionController::class, 'decline'])->name('connect.decline');

    Route::post('/connect/end/{connection_id}', [ConnectionController::class, 'endChat'])->name('connect.end');

    Route::get('/chat/{user}', [ChatController::class, 'showChat'])->name('chat.show');
    Route::get('/chats', [ChatController::class, 'showAllChats'])->name('chats.all');

    Route::post('/api/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/api/messages/{userId}/{chatUserId}', [ChatController::class, 'getMessages']);
    Route::get('/api/messages_latest/{userId}/{chatUserId}', [ChatController::class, 'getMessages10']);
    Route::delete('/api/messages/{messageId}', [ChatController::class, 'destroy']);
    Route::put('/api/messages/{messageId}', [ChatController::class, 'update']);

    Route::post('/upload_media', [ChatController::class, 'uploadMedia']);

    //Route::get('/api/messages_scroll', [ChatController::class, 'fetchMessages_infinite_scrolling']);

    Route::get('/fetch-messages', [ChatController::class, 'fetchMessages_offset']);

    Route::delete('deleteChat/{id}', [ChatController::class, 'deleteChat']);
});

require __DIR__.'/auth.php';
