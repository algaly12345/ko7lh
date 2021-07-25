<?php

use App\Company;
use App\GlobalSetting;
use Illuminate\Support\Str;
use App\CurrencyFormatSetting;
use App\Currency;
use Illuminate\Support\Facades\Artisan;

if (!function_exists('company')) {
    function company()
    {
        if (auth()->user()) {
            $company = Company::find(auth()->user()->company_id);
            return $company;
        }
        return false;
    }
}

if (!function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }
}

if (!function_exists('isRunningInConsoleOrSeeding')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isRunningInConsoleOrSeeding()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return app()->runningInConsole() || isSeedingData();
    }
}

if (!function_exists('isSeedingData')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isSeedingData()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return config('app.seeding');
    }
}


if (!function_exists('check_migrate_status')) {

    function check_migrate_status()
    {

        if (!session()->has('check_migrate_status')) {

            $status = Artisan::call('migrate:check');

            if ($status && !request()->ajax()) {
                Artisan::call('migrate', array('--force' => true)); //migrate database
                Artisan::call('optimize:clear');
            }

            session(['check_migrate_status' => 'Good']);
        }

        return session('check_migrate_status');
    }
}

if (!function_exists('currency_converted_price')) {

    function currency_converted_price($company_id, $price)
    {
        // Get exchange rates
        $from_currency = Company::withoutGlobalScope('company')->find($company_id)->currency->exchange_rate;
        $to_currency = GlobalSetting::first()->currency->exchange_rate;
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }
}

if (!function_exists('currency_convert_from_to')) {

    function currency_convert_from_to($from_currency_id, $to_currency_id, $price)
    {
        // Get exchange rates
        $from_currency = Currency::find($from_currency_id)->exchange_rate;
        $to_currency = Currency::find($to_currency_id)->exchange_rate;
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }
}

if (!function_exists('converted_original_price')) {

    function converted_original_price($company_id, $price)
    {
        // Get exchange rates
        $to_currency = Company::withoutGlobalScope('company')->find($company_id)->currency->exchange_rate;
        $from_currency = GlobalSetting::first()->currency->exchange_rate;
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }
}

//get currency symbol of user
if (!function_exists('my_currency_symbol')) {
    function my_currency_symbol()
    {
        if (!session()->has('my_currency_symbol')) {
            $currency_symbol = company() ? company()->currency->currency_symbol : global_setting()->currency->currency_symbol;
            session(['my_currency_symbol' => $currency_symbol]);
        }

        return session('my_currency_symbol');
    }
}

//format currency
if (!function_exists('currency_formatter')) {
    function currency_formatter($amount, $currency_symbol = null)
    {

        $formats = currency_format_setting();

        $currency_symbol = $currency_symbol ? $currency_symbol : global_setting()->currency->currency_symbol;

        $currency_position = $formats->currency_position;
        $no_of_decimal = !is_null($formats->no_of_decimal) ? $formats->no_of_decimal : '0';
        $thousand_separator = !is_null($formats->thousand_separator) ? $formats->thousand_separator : '';
        $decimal_separator = !is_null($formats->decimal_separator) ? $formats->decimal_separator : '0';
        $amount = number_format($amount, $no_of_decimal, $decimal_separator, $thousand_separator);
        switch ($currency_position) {
            case 'right':
                $amount = $currency_symbol . $amount;
                break;
            case 'left_with_space':
                $amount = $amount . ' ' . $currency_symbol;
                break;
            case 'right_with_space':
                $amount = $currency_symbol . ' ' . $amount;
                break;
            default:
                $amount = $amount . $currency_symbol;
                break;
        }

        return $amount;
    }
}
//create cache format currency settings to reduce database load
if (!function_exists('currency_format_setting')) {
    function currency_format_setting()
    {
        if (!cache()->has('currency_format_setting')) {
            $setting = CurrencyFormatSetting::first();
            cache(['currency_format_setting' => $setting],60 * 60 * 24);
        }

        return cache('currency_format_setting');
    }
}
//create cache global settings to reduce database load
if (!function_exists('global_setting')) {
    function global_setting()
    {
        if (!cache()->has('global_setting')) {
            $setting = GlobalSetting::first();
            cache(['global_setting' => $setting],60 * 60 * 24);
        }

        return cache('global_setting');
    }
}

//convert into Minutes of given Duration and Duration Type
if (!function_exists('convertToMinutes')) {
    function convertToMinutes($duration,$duration_type)
    {
        $durationTypeVal = 1; //minutes value
        if ($duration_type == 'minutes') {
            $durationTypeVal = 1;
        }elseif ($duration_type == 'hours') {
            $durationTypeVal = 60;
        }elseif($duration_type == 'days') {
            $durationTypeVal = 24 * 60;
        }elseif($duration_type == 'weeks') {
            $durationTypeVal = 7 * 24 * 60;
        }
        return ($duration * $durationTypeVal);
    }
}
