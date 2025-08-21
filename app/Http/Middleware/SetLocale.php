<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if ($request->has('lang') && in_array($request->get('lang'), ['en', 'ar'])) {
            session(['locale' => $request->get('lang')]);
        }

        App::setLocale(session('locale', config('app.locale')));

        return $next($request);
    }
}
