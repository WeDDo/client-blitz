<?php

namespace App\Http\Controllers;

use App\Models\DownloadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'title' => 'File ripper',
            'downloaded_files_count' => DownloadedFile::query()->count(),
            'favorited_files_count' => count(Storage::disk('public')->files('favorites')),
        ]);
    }
}
