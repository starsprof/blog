<?php


namespace App\Core;


use App\Controllers\PageController;
use Dotenv\Dotenv;
use Slim\App;
use Slim\Container;

class Bootstrap
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var App
     */
    private $app;

    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->loadEnv();
        $this->container = new Container($this->containerConfig());
        $this->bindDependencies();
        $this->app = new App($this->container);
        $this->bindRoutes();
    }

    /**
     * Load .env
     */
    private function loadEnv()
    {
        $dotenv = Dotenv::create(getenv('ROOT').'/..');
        $dotenv->overload();
    }

    /**
     * @return array
     */
    private function containerConfig(): array
    {
        return [
            'settings' => [
                'displayErrorDetails' => getenv('displayErrorDetails'),
            ],
        ];
    }

    private function bindDependencies()
    {
        $configurationView = [
            'cache' => false,
            'debug' => true
            //'cache' => __DIR__.'/../Storage/Cache'
        ];

        $container = $this->container;
        $this->container['view'] = function ($container) use ($configurationView) {
            $view = new \Slim\Views\Twig(getenv('ROOT').'/Views', $configurationView);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
            return $view;
        };
    }

    private function bindRoutes()
    {
        $this->app->get('/', PageController::class.':home');
    }

    public function run()
    {
        $this->app->run();
    }
}