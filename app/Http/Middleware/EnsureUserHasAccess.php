<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAccess
{
    public function handle(Request $request, Closure $next, $type): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

  
        if ($user->is_admin == 1) {
            return $next($request);
        }


        switch ($type) {
            case 'admin':
                return redirect()->to('https://businesstools.valuenationapp.com');

            case 'role':
                if ($user->roles()->exists()) {
                    return $next($request);
                }
                break;

            case 'regular':
                if (!$user->is_admin && !$user->roles()->exists()) {
                    return $next($request);
                }
                break;
        }

        return redirect()->to('https://businesstools.valuenationapp.com');
    }
}
