<?php


namespace App\Controllers;


use App\Models\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController
{
    /**
     * @var UserRepositoryInterface;
     */
    private $userRepository;
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->userRepository = $this->container->get(UserRepositoryInterface::class);
    }

    public function profile(Request $request, Response $response)
    {
        if($request->isGet()) {
            $user = $this->auth->reloadUser($this->auth->user());
            return $this->view->render($response, 'profile/index.twig', ['user' => $user]);
        }
        $params = $request->getParsedBody();
        var_dump($params);
    }


}