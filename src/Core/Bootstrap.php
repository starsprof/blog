<?php


namespace App\Core;


use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\Middleware\AuthMiddleware;
use App\Controllers\Middleware\GuestMiddleware;
use App\Controllers\PageController;
use App\Controllers\UserController;
use App\Models\Auth;
use App\Models\Repositories\BaseRepository;
use App\Models\Repositories\CategoryRepository;
use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\UserRepository;
use App\Models\Repositories\UserRepositoryInterface;
use App\Models\User;
use DebugBar\StandardDebugBar;
use Dotenv\Dotenv;
use Slim\App;
use Slim\Container;

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
        $this->container = new Container($this->containerConfig());
        $this->bindDependencies();
        $this->app = new App($this->container);
        $this->bindRoutes();

        $devMode = filter_var(getenv('DEV_MODE'), FILTER_VALIDATE_BOOLEAN);
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
                'displayErrorDetails' => getenv('displayErrorDetails')
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
        $this->container[User::class] = function ($container) {
            return new User($container);
        };
        $this->container[Auth::class] = function ($container) {
            return new Auth($container, $container->get(UserRepositoryInterface::class));
        };
        $this->container['view'] = function ($container) use ($configurationView) {
            $view = new \Slim\Views\Twig(getenv('ROOT') . '/Views', $configurationView);
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

            $view->getEnvironment()->addGlobal('auth', $container->get(Auth::class));

            return $view;
        };

    }

    private function setUpDebugBar()
    {
        $debugBar = new StandardDebugBar();
        $view = $this->container['view'];
        $debugBarRender = $debugBar->getJavascriptRenderer();
        $debugBarRender->setBaseUrl('\assets\debugbar');
        $view->getEnvironment()->addGlobal('debugHead', $debugBarRender->renderHead());
        $view->getEnvironment()->addGlobal('debugBody', $debugBarRender->render());
    }

    private function bindRoutes()
    {
        $this->app
            ->get('/', PageController::class.':home')
            ->setName('home');

        $this->app
            ->map(['GET', 'POST'], '/signin', AuthController::class.':signIn')
            ->setName('signIn')
            ->add(new GuestMiddleware($this->container));

        $this->app
            ->map(['GET', 'POST'], '/signup', AuthController::class.':signUp')
            ->add(new GuestMiddleware($this->container));

        $this->app
            ->get('/signOut', AuthController::class.':signOut')
            ->add(new AuthMiddleware($this->container));

        $this->app
            ->map(['GET', 'POST'], '/profile', UserController::class.':profile')
            ->add(new AuthMiddleware($this->container));
        $app = &$this->app;
        $this->app
            ->group('/admin', function () use ($app) {
            $app->group('/categories', function () use ($app){
                $app->get('[/{page:[0-9]+}]', CategoryController::class.':adminIndex');
                $app->delete('/delete', CategoryController::class.':adminRemove');
                $app->map(['GET', 'POST'], '/add', CategoryController::class.':adminAdd');
            });
            })->add(new AuthMiddleware($this->container));
    }

    public function run()
    {
        $this->app->run();
    }
}