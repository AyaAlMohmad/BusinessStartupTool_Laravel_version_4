<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التحقق من تسجيل الدخول أولاً
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // 2. إذا كان أدمن، امنحه الوصول مباشرة
        if ($user->is_admin == 1) {
            return $next($request);
        }

        // 3. التحقق من وجود أي رول للمستخدم
        if ($user->roles()->exists()) {
            return $next($request);
        }

        // 4. إذا لم يكن لديه رول، إعادة توجيه
        return redirect()->to('https://businesstools.valuenationapp.com');
    }
}
