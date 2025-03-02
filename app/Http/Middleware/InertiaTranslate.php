<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class InertiaTranslate
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

        $translations = $this->getTranslations($locale);

        Inertia::share([
            'translations' => $translations,
            'locale' => $locale,
        ]);

        return $next($request);
    }

    protected function getTranslations($locale): array
    {
        $path = base_path("lang/$locale");
        $translations = [];

        foreach (File::allFiles($path) as $file) {
            $relativePath = str_replace(['/', '\\'], '.', $file->getRelativePathname());
            $key = pathinfo($relativePath, PATHINFO_FILENAME);

            $groupKey = trim(str_replace(['/', '\\'], '.', pathinfo($relativePath, PATHINFO_DIRNAME)), '.');
            $fullKey = $groupKey ? "{$groupKey}.{$key}" : $key;

            $filePath = $file->getPathname();
            $fileContents = include $filePath;

            if (is_array($fileContents)) {
                $translations = array_merge_recursive($translations, $this->flattenArray($fileContents, $fullKey));
            }
        }

        return $translations;
    }

    /**
     * Recursively flattens a nested array to dot notation.
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }



}
