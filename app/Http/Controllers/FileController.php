<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FileController extends Controller
{
    private string $translate = 'modules/file';

    public function index(): Response
    {
        $baseDirectory = '';
        $path = request()->query('path', '');
        $sortOrder = request()->query('sort', 'desc');

        $recursive = boolval(request()->query('recursive') ?? cache()->get('files_recursive'));
        if ($recursive !== cache()->get('files_recursive')) {
            cache()->put('files_recursive', $recursive);
        }

        $fullPath = $baseDirectory . ($path ? "/$path" : '');

        $directories = [];
        foreach (Storage::disk('public')->directories($fullPath) as $dir) {
            $directories[] = [
                'name' => basename($dir),
                'path' => $dir,
            ];
        }

        $files = collect($recursive ? Storage::disk('public')->allFiles($fullPath) : Storage::disk('public')->files($fullPath))
            ->map(function ($file) {
                $filePath = Storage::disk('public')->path($file);
                $resolution = null;

                if (file_exists($filePath)) {
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $resolution = $this->getImageResolution($filePath);
                    } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'webm'])) {
                        $resolution = $this->getVideoResolution($filePath);
                    }
                }

                return [
                    'name' => basename($file),
                    'url' => Storage::url($file),
                    'resolution' => $resolution,
                    'created_at' => file_exists($filePath) ? filemtime($filePath) : null,
                ];
            })
            ->filter(fn($file) => $file['created_at'] !== null)
            ->sortByDesc('created_at')
            ->take(500)
            ->values();

        return Inertia::render('modules/Files', [
            'path' => $path,
            'directories' => $directories,
            'files' => $files,
            'sortOrder' => $sortOrder,
            'recursive' => $recursive
        ]);
    }

    public function destroy(): RedirectResponse
    {
        $url = str_replace('/storage/', '', request()->input('url'));

        if (!Storage::disk('public')->exists($url)) {
            return back()->with('error', __("$this->translate.not_found_error"));
        }
        Storage::disk('public')->delete($url);

        return back()->with('success', __("$this->translate.delete_success", [
            'file' => $url
        ]));
    }

    public function massDelete(): RedirectResponse
    {
        $urls = request()->input('urls', []);

        if (empty($urls)) {
            return back()->with('error', __("$this->translate.no_files_selected_error"));
        }

        foreach ($urls as $url) {
            $url = str_replace('/storage/', '', $url);
            if (Storage::disk('public')->exists($url)) {
                Storage::disk('public')->delete($url);
            }
        }

        return back()->with('success', __("$this->translate.mass_delete_success", [
            'file_count' => count($urls)
        ]));
    }

    public function addToFavorites(): RedirectResponse
    {
        $urls = request()->input('urls', []);
        if (empty($urls)) {
            return back()->with('error', __("$this->translate.no_files_selected_error"));
        }

        foreach ($urls as $url) {
            $url = str_replace('/storage/', '', $url);
            $favoriteFilePath = "/favorites/" . basename($url);

            if (Storage::disk('public')->exists($url)) {
                Storage::disk('public')->copy($url, $favoriteFilePath);
            }
        }

        return back()->with('success', __("$this->translate.add_to_favorites_success", [
            'file_count' => count($urls)
        ]));
    }


    /**
     * Get image resolution.
     */
    private function getImageResolution(string $filePath): ?string
    {
        $size = @getimagesize($filePath);
        return $size ? "{$size[0]}x{$size[1]}" : null;
    }

    /**
     * Get video resolution using ffprobe.
     */
    private function getVideoResolution(string $filePath): ?string
    {
        $ffprobePath = '/usr/bin/ffprobe'; // Adjust this if necessary
        if (!file_exists($ffprobePath)) {
            return null; // ffprobe not installed
        }

        $cmd = "$ffprobePath -v error -select_streams v:0 -show_entries stream=width,height -of csv=p=0:s=x \"$filePath\"";
        $output = shell_exec($cmd);
        return trim($output) ?: null;
    }
}
