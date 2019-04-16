<?php


namespace App\Controllers;


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

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->view = $this->container->get('view');
    }
}