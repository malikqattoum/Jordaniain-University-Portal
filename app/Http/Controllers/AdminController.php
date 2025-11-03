<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !$admin->is_active || !Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة أو حسابك غير مفعل.'],
            ]);
        }

        // Update last login
        $admin->updateLastLogin();

        return redirect()->intended()->route('admin.dashboard');
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.dashboard', compact('admin'));
    }

    /**
     * Show admin profile.
     */
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $admin->update($data);

        return redirect()->route('admin.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }
}
