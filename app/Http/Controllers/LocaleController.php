<?php

namespace App\Http\Controllers;

use App\Models\DownloadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LocaleController extends Controller
{
  public function setLocale(): RedirectResponse
  {
      $locale = request()->input('locale', 'en');
      session()->put('locale', $locale);
      app()->setLocale($locale);

      return back();
  }
}
