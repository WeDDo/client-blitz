<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
  public function setLocale(): RedirectResponse
  {
      $locale = request()->input('locale', 'en');
      session()->put('locale', $locale);
      app()->setLocale($locale);

      return back()->with('success', __('app/locale.locale_changed', [
          'locale' => __("app/locale.locales.$locale"),
      ]));
  }
}
