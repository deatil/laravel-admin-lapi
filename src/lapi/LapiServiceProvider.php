<?php

namespace Lake\Admin\Lapi;

use Illuminate\Support\ServiceProvider;

use Lake\Admin\Lapi\Contracts\Response as ResponseContracts;
use Lake\Admin\Lapi\Http\Response as ResponseHttp;

use Lake\Admin\Lapi\Command\Install;
use Lake\Admin\Lapi\Command\Uninstall;
use Lake\Admin\Lapi\Command\ImportRoute;

class LapiServiceProvider extends ServiceProvider
{
    protected $commands = [
        Install::class,
        Uninstall::class,
        ImportRoute::class,
    ];
    
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'api.lapi' => Middleware\Lapi::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'lapi' => [
            'api.lapi',
        ],
    ];

    public function register()
    {
        $this->registerRouteMiddleware();
        
        $this->commands($this->commands);
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lapi');

        $this->app->bind('lapiJson', ResponseContracts::class);
        $this->app->bind(ResponseContracts::class, ResponseHttp::class);
        
        Lapi::boot();
    }
    
    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
    
}
