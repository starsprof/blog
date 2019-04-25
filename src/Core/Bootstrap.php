<?php


namespace App\Core;


use App\Models\Auth;
use App\Models\Repositories\CategoryRepository;
use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\PostRepository;
use App\Models\Repositories\PostRepositoryInterface;
use App\Models\Repositories\UserRepository;
use App\Models\Repositories\UserRepositoryInterface;
use App\Models\User;
use Dotenv\Dotenv;
use Slim\App;
use Slim\Container;
use Tracy\Debugger;


class Bootstrap
{
    /**
     * @var Container
     */
    public $container;
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
        $devMode = filter_var(getenv('DEV_MODE'), FILTER_VALIDATE_BOOLEAN);

        if($devMode) {
            Debugger::enable(false);
        }

        $this->container = new Container($this->containerConfig());
        $this->bindDependencies();
        $this->app = new App($this->container);
        Router::setRoutes($this->app);
        $this->container['devMode'] = $devMode;

        if($devMode) {
            $this->setUpDebugBar();
        }

    }

    /**
     * Load .env
     */
    private function loadEnv()
    {
        $dotenv = Dotenv::create(getenv('ROOT').'/..');
        $dotenv->load();
    }

    /**
     * @return array
     */
    private function containerConfig(): array
    {
        return [
            'settings' => [
                'displayErrorDetails' => getenv('displayErrorDetails'),
                'addContentLengthHeader' => false,
                'tracy' => [
                    'showPhpInfoPanel' => 1,
                    'showSlimRouterPanel' => 0,
                    'showSlimEnvironmentPanel' => 0,
                    'showSlimRequestPanel' => 1,
                    'showSlimResponsePanel' => 1,
                    'showSlimContainer' => 0,
                    'showTwigPanel' => 0,
                    'showProfilerPanel' => 0,
                    'showVendorVersionsPanel' => 0,
                    'showIncludedFiles' => 0,
                    'configs' => [
                        'ConsoleNoLogin' => 0,
                        'ProfilerPanel' => [
                            'show' => [
                                'memoryUsageChart' => 1, // or false
                                'shortProfiles' => true, // or false
                                'timeLines' => true // or false
                            ]
                        ]
                    ]
                ]
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

        $container = &$this->container;
        $this->container[UserRepositoryInterface::class] = function ($container) {
            return new UserRepository($container);
        };
        $this->container[CategoryRepositoryInterface::class] = function ($container){
            return new CategoryRepository($container);
        };
        $this->container[PostRepositoryInterface::class] = function ($container){
            return new PostRepository($container);
        };
        $this->container[User::class] = function ($container) {
            return new User($container);
        };
        $this->container[Auth::class] = function ($container) {
            return new Auth($container, $container->get(UserRepositoryInterface::class));
        };
        $container['flash'] = function () {
            return new \Slim\Flash\Messages();
        };
        $this->container['view'] = function ($container) use ($configurationView) {
            $view = new \Slim\Views\Twig(getenv('ROOT') . '/Views', $configurationView);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
            $view->addExtension(new \Knlv\Slim\Views\TwigMessages(
                new \Slim\Flash\Messages()
            ));
            $view->getEnvironment()->addGlobal('auth', $container->get(Auth::class));
            return $view;
        };

    }

    private function setUpDebugBar()
    {
        unset($this->app->getContainer()['errorHandler']);
        $this->container['twig_profile'] = function () {
            return new \Twig\Profiler\Profile();
        };
        $view = $this->container['view'];
        $view->addExtension(new \Twig\Extension\ProfilerExtension($this->container['twig_profile']));
        $view->addExtension(new \Twig\Extension\DebugExtension());
        //Debugger::$logDirectory = getenv('ROOT').'/../logs/';
        Debugger::getBar()->addPanel(new \App\Core\Utils\TracySessionBar());
        Debugger::getBar()->addPanel(new \App\Core\Utils\TracyDBBar());
        $this->app->add(new \RunTracy\Middlewares\TracyMiddleware($this->app));


    }

    public function run()
    {
        $this->app->run();
    }
}