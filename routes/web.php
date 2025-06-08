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

Route::post('/blueprint', [BlueprintController::class, 'store'])->name('blueprints.store');
Route::patch('/blueprint/{blueprint}', [BlueprintController::class, 'update'])
    ->middleware('auth')
    ->name('blueprints.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/generator', function () {
    $data = [
        'phpVersions' => \App\Enums\PhpVersion::values(),
        'wordpressVersions' => \App\Enums\WordpressVersion::values(),
    ];
    
    // Include any flashed data from blueprint creation
    if (session()->has('data')) {
        $data['data'] = session()->get('data');
    }
    
    return Inertia::render('Generator', $data);
})->name('generator');

// Handle preflight OPTIONS requests for CORS
Route::options('/blueprint/{id}', function () {
    return response('')
        ->header('Access-Control-Allow-Origin', 'https://playground.wordpress.net')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

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
    
    return response()->json($playgroundBlueprint)
        ->header('Access-Control-Allow-Origin', 'https://playground.wordpress.net')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->name('blueprint.show');

require __DIR__.'/auth.php';
