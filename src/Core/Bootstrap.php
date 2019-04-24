<?php


namespace App\Core;


use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\Middleware\AuthMiddleware;
use App\Controllers\Middleware\GuestMiddleware;
use App\Controllers\PageController;
use App\Controllers\PostController;
use App\Controllers\UserController;
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
        $this->bindRoutes();
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
                $app->get('/edit/{id:[0-9]+}', CategoryController::class.':getEdit');
                $app->post('/edit', CategoryController::class.':postEdit');
            });
            })->add(new AuthMiddleware($this->container));
        $this->app->group('/posts', function () use ($app){
            $app->get('/admin[/{page:[0-9]+}]', PostController::class.':adminIndex');
            $app->map(['GET', 'POST'], '/add' ,PostController::class.':add');
        });
    }

    public function run()
    {
        $this->app->run();
    }
}