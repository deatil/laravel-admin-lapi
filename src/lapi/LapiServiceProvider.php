<?php

namespace Lake\Admin\Lapi;

use Illuminate\Support\ServiceProvider;

use Lake\Admin\Lapi\Contracts\Response as ResponseContract;
use Lake\Admin\Lapi\Contracts\ApiCheck as ApiCheckContract;
use Lake\Admin\Lapi\Http\Response as ResponseHttp;
use Lake\Admin\Lapi\Service\ApiAuth as ApiAuthService;

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

        // json响应
        $this->app->bind('lapi.json', ResponseContract::class);
        $this->app->bind(ResponseContract::class, ResponseHttp::class);
        
        // api检测
        $this->app->bind('lapi.check', ApiCheckContract::class);
        $this->app->bind(ApiCheckContract::class, ApiAuthService::class);
        
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
