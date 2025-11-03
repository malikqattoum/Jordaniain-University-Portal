<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'يجب تسجيل الدخول كمشرف أولاً.');
        }

        $admin = Auth::guard('admin')->user();

        if (!$admin->is_active) {
            return redirect()->route('admin.login')->with('error', 'حسابك غير مفعل.');
        }

        if (!empty($roles) && !$admin->hasAnyRole($roles)) {
            return redirect()->route('admin.dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
