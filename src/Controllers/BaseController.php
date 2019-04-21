<?php


namespace App\Controllers;


use App\Models\Auth;
use Psr\Container\ContainerInterface;
use \Slim\Views\Twig;

class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var Twig
     */
    protected $view;
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * BaseController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->view = $this->container->get('view');
        $this->auth = $this->container->get(Auth::class);
    }
}