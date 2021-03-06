<?php

namespace Yab\Quazar\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Yab\Quazar\Middleware\isAjax;

class QuazarRouteProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Yab\Quazar\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
        ], function ($router) {
            $router->middleware('isAjax', isAjax::class);
            require __DIR__.'/../Routes/quarx.php';
        });

        $this->namespace = 'App\Http\Controllers\Quazar';

        $router->group([
            'namespace' => $this->namespace,
        ], function ($router) {
            $router->middleware('isAjax', isAjax::class);
            require __DIR__.'/../Routes/app.php';
        });
    }
}
