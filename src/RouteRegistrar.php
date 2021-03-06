<?php

namespace Fikrimi\Pipe;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @param string $prefix
     * @return void
     */
    public function all($prefix = 'pipe')
    {
        $this->router->group([
            'as'        => 'pipe::',
            'prefix'    => $prefix,
            'namespace' => '\Fikrimi\Pipe\Http\Controllers',
        ], function (Router $router) {

            if (config('app.env') === 'testing') {
                $router->get('/tests/application_exception', function () {
                    throw new \Fikrimi\Pipe\Exceptions\ApplicationException('exception thrown');
                });
            }

            $router->get('/', function () {
                return redirect()->route('pipe::projects.index');
            });

            $router->post('projects/{project}/build', [
                'uses' => 'BuildController@build',
                'as'   => 'build',
            ]);
            $router->post('builds/{build}/switch', [
                'uses' => 'BuildController@switchBuild',
                'as'   => 'builds.switch',
            ]);
            $router->resource('builds', 'BuildController', [
                'except' => ['create', 'store'],
            ]);
            $router->resource('projects', 'ProjectController');
            $router->resource('credentials', 'CredentialController')
                ->only(['index', 'create', 'store', 'destroy']);

            $router->post('stacks/{stack}/duplicate', [
                'uses' => 'StackController@duplicate',
                'as'   => 'stacks.duplicate',
            ]);

            $router->resource('stacks', 'StackController');

            $router->post('webhook/{project}', [
                'uses' => 'BuildController@build',
                'as'   => 'builds.webhook',
            ]);
        });
    }
}
