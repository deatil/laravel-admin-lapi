<?php

namespace Lake\Admin\Lapi;

use Illuminate\Support\ServiceProvider;

class LapiServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lapi');

        Lapi::boot();
    }
}
