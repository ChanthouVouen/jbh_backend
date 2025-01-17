<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 

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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('/dashboard/dashboard');
})->middleware('auth');

Route::get('/login', function () {
    return view('/auth/login');
});


Route::get('/auth/google/redirect', function (){
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function (Request $request) {

    $googleUser = Socialite::driver('google')->stateless()->user();

 
    $user = User::updateOrCreate(
        ['google_id' => $googleUser->id], 
        [
            'name' => $googleUser->name, 
            'email' => $googleUser->email, 
            'password' => $user->password ?? Hash::make(Str::random(12)),
            'remember_token' => $googleUser->token,  // Optional: Store the access token for future use
        ]
    );

    Auth::login($user);
    return redirect('/dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->middleware('auth');