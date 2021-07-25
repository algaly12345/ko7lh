<?php

namespace App\Observers;

use App\BusinessService;
use App\Company;
use App\Currency;
use App\GlobalSetting;
use App\CompanyCurrency;
use App\Deal;
use App\Product;

class CurrencyObserver
{
    /**
     * Handle the currency "created" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function created(Currency $currency)
    {

    }

    /**
     * Handle the currency "updated" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function updated(Currency $currency)
    {
        //
    }

    /**
     * Handle the currency "deleting" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function deleting(Currency $currency)
    {
        //
    }/**
     * Handle the currency "deleted" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function deleted(Currency $currency)
    {
        //
    }
    /**
     * Handle the currency "restored" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function restored(Currency $currency)
    {
        //
    }

    /**
     * Handle the currency "force deleted" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function forceDeleted(Currency $currency)
    {
        //
    }
}
