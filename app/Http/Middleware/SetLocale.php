<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['pt_BR', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $request->cookie('locale');
        }

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $this->localeFromHeader($request->header('Accept-Language', ''));
        }

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.locale', 'en');
        }

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'en';
        }

        $request->session()->put('locale', $locale);
        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    private function localeFromHeader(string $header): ?string
    {
        $languages = [];

        foreach (explode(',', $header) as $entry) {
            [$tag, $parameters] = array_pad(explode(';', trim($entry), 2), 2, '');
            preg_match('/(?:^|;)\s*q=([0-9.]+)/', $parameters, $matches);
            $languages[] = [
                'tag' => strtolower(str_replace('_', '-', trim($tag))),
                'quality' => isset($matches[1]) ? (float) $matches[1] : 1.0,
                'position' => count($languages),
            ];
        }

        usort($languages, fn (array $a, array $b) => ($b['quality'] <=> $a['quality']) ?: ($a['position'] <=> $b['position']));

        foreach ($languages as $language) {
            if ($language['quality'] <= 0) {
                continue;
            }

            if (str_starts_with($language['tag'], 'pt')) {
                return 'pt_BR';
            }

            if (str_starts_with($language['tag'], 'en')) {
                return 'en';
            }
        }

        return null;
    }
}
