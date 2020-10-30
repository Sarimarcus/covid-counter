<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\Localization;
Use \Carbon\Carbon;

class Home extends Controller
{
    public function index()
    {
        $geoData = geoip()->getLocation(env('IP'));
        $date = Carbon::today();
        $today = ucwords($date->isoFormat('dddd Do MMMM'));
        $quote = __('quotes.' . rand(0,10));

        $start = 'unknown';
        if (isset($geoData['country'])){
            // Lockdown start date
            // https://en.wikipedia.org/wiki/2020_coronavirus_pandemic_in_Europe
            switch ($geoData['country']) {
                case 'Spain':
                    $start = '2020-03-14 00:00:01';
                    break;

                case 'United States':
                    $start = '2020-03-14 00:00:01';
                    break;

                case 'France':
                    //$start = '2020-03-16 12:00:00';
                    $start = '2020-10-30 23:59:59';
                    break;

                case 'Serbia':
                    $start = '2020-03-17 12:00:00';
                    break;

                default:
                    \Log::warning('lockdown start date not found for ' . $geoData['country']);
                    break;
            }
        }

        // Handling country
        $locale = \App::getLocale();
        isset($geoData['country']) ? __('country.' . strtolower($geoData['country'])) : '';

        if (isset($geoData['country'])) {
            $coutryName = 'country.' . strtolower($geoData['country']);
            if (\Lang::has($coutryName, $locale)) {
                $coutryName = __('country.' . strtolower($geoData['country']));
            } else {
                $coutryName = $geoData['country'];
                \Log::warning('translation not found for ' . $geoData['country']);
            }
        } else $coutryName = '';

        return view('covid.index',[
            'country' => $coutryName,
            'city' => isset($geoData['city']) ? $geoData['city'] : '',
            'start' => isset($start) ? $start : 'unknown',
            'today' => $today,
            'quote' => $quote,
            'url' => env('APP_URL')
        ]);
    }
}
