<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is deactivated. Please contact administrator.']);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Log activity
            \App\Models\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'Login',
                'ip_address' => $request->ip(),
                'details' => 'User logged in successfully.',
                'activity_at' => now()
            ]);

            // Redirect based on role
            if ($user->isNetworkAdmin()) {
                return redirect()->route('network-devices.index')->with('success', 'Welcome back, ' . $user->name . '!');
            }
            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,security_analyst,network_admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
            ]);

            // Log activity
            \App\Models\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'Registration',
                'ip_address' => $request->ip(),
                'details' => 'User registered account with role: ' . $user->role_name,
                'activity_at' => now()
            ]);

            Auth::login($user);

            if ($user->isNetworkAdmin()) {
                return redirect()->route('network-devices.index')->with('success', 'Account created successfully! Welcome ' . $user->name . '!');
            }
            return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome ' . $user->name . '!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'Logout',
                'ip_address' => $request->ip(),
                'details' => 'User logged out successfully.',
                'activity_at' => now()
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}