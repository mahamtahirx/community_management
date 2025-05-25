<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        try {
            Http::post('http://localhost:3001/send-email', [
                'to' => $user->email,
                'subject' => 'Welcome to Community Management!',
                'html' => "<h1>Welcome, {$user->name}!</h1><p>Thanks for signing up. Start joining communities now!</p>",
            ]);
        } catch (\Exception $e) {
            \Log::error('Registration email failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            try {
                Http::post('http://localhost:3001/send-email', [
                    'to' => Auth::user()->email,
                    'subject' => 'Login Notification',
                    'html' => "<p>Hello {$request->user()->name}, you just logged into your account at " . now()->toDayDateTimeString() . ".</p>",
                ]);
            } catch (\Exception $e) {
                \Log::error('Login email failed: ' . $e->getMessage());
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}