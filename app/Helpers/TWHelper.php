<?php

namespace App\Helpers;

class TWHelper
{
    static function merge(...$classes): string {
        $conflictGroups = [
            'bg' => '/^bg-[\w-]+$/',
            'text' => '/^text-[\w-]+$/',
            'p' => '/^p[trblxy]?-[\w-]+$/',
            'm' => '/^m[trblxy]?-[\w-]+$/',
            'rounded' => '/^rounded(-[trbl]{0,2})?(-[^\s]+)?$/',
            'border' => '/^border(-(t|r|b|l|x|y))?(-[\w-]+)?$/',
            // Add more groups as needed
        ];

        $classMap = [];
        $finalClasses = [];

        foreach ($classes as $class) {
            if (is_array($class)) {
                $class = implode(' ', $class);
            }

            foreach (explode(' ', trim($class)) as $cls) {
                $matched = false;
                foreach ($conflictGroups as $group => $pattern) {
                    if (preg_match($pattern, $cls)) {
                        $classMap[$group] = $cls;
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    $finalClasses[$cls] = true; // no conflict, keep all
                }
            }
        }

        // Merge non-conflicting and latest of each group
        return implode(' ', array_merge(array_values($classMap), array_keys($finalClasses)));
    }

}