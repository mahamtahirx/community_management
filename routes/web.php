<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $userCommunities = auth()->user()->communities;
        return view('dashboard', compact('userCommunities'));
    })->name('dashboard');

    // Communities
    Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/create', [CommunityController::class, 'showCreateForm'])->name('communities.create.form');
    Route::post('/communities', [CommunityController::class, 'create'])->name('communities.create');
    Route::get('/communities/{community}', [CommunityController::class, 'show'])->name('communities.show');
    Route::get('/communities/{community}/members', [CommunityController::class, 'members'])->name('communities.members');
    Route::post('/communities/{community}/join', [CommunityController::class, 'join'])->name('communities.join');
    Route::put('/communities/{community}/members/{user}/update-role', [CommunityController::class, 'updateRole'])
        ->name('communities.update-role');
    Route::delete('/communities/{community}/members/{user}', [CommunityController::class, 'removeMember'])
        ->name('communities.remove-member');

    // Events
    Route::get('/communities/{community}/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/communities/{community}/events/create', [EventController::class, 'showCreateForm'])->name('events.create.form');
    Route::post('/communities/{community}/events', [EventController::class, 'create'])->name('events.create');
    Route::get('/communities/{community}/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');
    Route::delete('/communities/{community}/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // ✅ Newly added for editing and updating events
    Route::get('/communities/{community}/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/communities/{community}/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::post('/communities/{community}/events/{event}/remind', [EventController::class, 'sendReminder'])
    ->name('events.remind');

});
