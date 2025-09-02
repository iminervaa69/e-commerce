<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{
    /**
     * Toggle theme and save to cookie
     */
    public function toggle(Request $request): JsonResponse
    {
        $currentTheme = $request->cookie('theme', 'light');
        $newTheme = $currentTheme === 'dark' ? 'light' : 'dark';
        
        return response()->json([
            'theme' => $newTheme,
            'success' => true
        ])->cookie('theme', $newTheme, 525600); // Cookie lasts 1 year
    }
    
    /**
     * Set specific theme
     */
    public function set(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|in:light,dark'
        ]);
        
        $theme = $request->input('theme');
        
        return response()->json([
            'theme' => $theme,
            'success' => true
        ])->cookie('theme', $theme, 525600);
    }
}