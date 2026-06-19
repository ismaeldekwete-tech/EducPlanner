<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'fr');

        if (auth()->check()) {
            $locale = auth()->user()->lang ?? 'fr';
        } elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
