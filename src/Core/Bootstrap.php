<?php


namespace App\Core;


use App\Controllers\AuthController;
use App\Controllers\PageController;
use App\Models\Auth;
use App\Models\Repositories\BaseRepository;
use App\Models\Repositories\UserRepository;
use DebugBar\StandardDebugBar;
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
        $container[Auth::class] = function ($container) {
            return new Auth($container, new UserRepository());
        };
        $this->container['view'] = function ($container) use ($configurationView) {
            $view = new \Slim\Views\Twig(getenv('ROOT').'/Views', $configurationView);
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
        $pdo = new \DebugBar\DataCollector\PDO\TraceablePDO(BaseRepository::getPDO());
        $debugBar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($pdo));
        $view = $this->container['view'];
        $debugBarRender = $debugBar->getJavascriptRenderer();
        $debugBarRender->setBaseUrl('\assets\debugbar');
        $view->getEnvironment()->addGlobal('debugHead', $debugBarRender->renderHead());
        $view->getEnvironment()->addGlobal('debugBody', $debugBarRender->render());
    }

    private function bindRoutes()
    {
        $this->app->get('/', PageController::class.':home');
        $this->app->map(['GET', 'POST'], '/signin', AuthController::class.':signIn');
        $this->app->map(['GET', 'POST'], '/signup', AuthController::class.':signUp');
        $this->app->get('/signOut', AuthController::class.':signOut');
    }

    public function run()
    {
        $this->app->run();
    }
}