<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlueprintController;
use App\Models\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'blueprints' => Blueprint::latest()->take(12)->get(),
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $blueprints = $user ? Blueprint::where('user_id', $user->id)
        ->with('statistics')
        ->latest()
        ->get() : collect();
        
    return Inertia::render('Dashboard', [
        'blueprints' => $blueprints
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/generator', function () {
    // Logic for the generator
    return Inertia::render('Generator', [
    ]);
})->name('generator');

Route::get('/blueprint/{id}', function ($id) {
    $blueprint = Blueprint::findOrFail($id);
    
    // Format blueprint as WordPress Playground schema
    $playgroundBlueprint = [
        '$schema' => 'https://playground.wordpress.net/blueprint-schema.json',
        'meta' => [
            'title' => $blueprint->name,
            'description' => $blueprint->description ?? '',
            'author' => $blueprint->user ? $blueprint->user->name : 'Anonymous',
            'categories' => ['generated'] // You can customize this based on your needs
        ],
        'landingPage' => '/wp-admin/', // Default landing page, you can customize
        'login' => true,
        'preferredVersions' => [
            'php' => $blueprint->php_version,
            'wp' => $blueprint->wordpress_version
        ],
        'features' => [
            'networking' => true // Default value, you can customize
        ],
        'steps' => $blueprint->steps ?? []
    ];
    
    return response()->json($playgroundBlueprint);
})->name('blueprint.show');

require __DIR__.'/auth.php';
