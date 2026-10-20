<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(['pt_BR', 'en'])],
        ]);

        $locale = $validated['locale'];
        $request->session()->put('locale', $locale);
        App::setLocale($locale);
        Carbon::setLocale($locale);

        $referer = $request->headers->get('referer');
        $refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;
        $target = $refererHost === $request->getHost() ? $referer : route('reviews');

        return redirect()->to($target)->withCookie(cookie(
            'locale', $locale, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'lax'
        ));
    }
}
