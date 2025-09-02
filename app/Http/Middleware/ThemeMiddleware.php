<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get theme from cookie, default to 'light'
        $theme = $request->cookie('theme', 'light');
        
        // Share theme globally with all views
        view()->share('currentTheme', $theme);
        view()->share('isDarkMode', $theme === 'dark');
        
        return $next($request);
    }
}