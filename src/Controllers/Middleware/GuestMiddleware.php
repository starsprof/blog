<?php


namespace App\Controllers\Middleware;


use App\Models\Auth;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GuestMiddleware extends BaseMiddleware
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
        if( ! $auth->isGuest()) {
            return $response->withRedirect($this->container->router->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }

}