<?php

namespace App\Controllers\Middleware;

use App\Models\Auth;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware extends BaseMiddleware
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function __invoke(Request $request,Response $response, $next)
    {
        /** @var Auth $auth */
        $auth = $this->container->get(Auth::class);
        if(! $auth->isAuth()) {
            return $response->withRedirect($this->container->router->pathFor('signIn'));
        }
        $response = $next($request, $response);
        return $response;
    }
}