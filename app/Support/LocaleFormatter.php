<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class LocaleFormatter
{
    public static function date(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '';
        }

        return $date->format(app()->getLocale() === 'pt_BR' ? 'd/m/Y' : 'm/d/Y');
    }

    public static function relative(?DateTimeInterface $date): string
    {
        return $date === null ? '' : Carbon::instance($date)->locale(app()->getLocale())->diffForHumans();
    }

    public static function number(float|int $number, int $decimals = 0): string
    {
        $portuguese = app()->getLocale() === 'pt_BR';

        return number_format($number, $decimals, $portuguese ? ',' : '.', $portuguese ? '.' : ',');
    }
}
