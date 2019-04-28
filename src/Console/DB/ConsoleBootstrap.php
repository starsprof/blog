<?php


namespace App\Console\DB;

use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\CategoryRepository;
use App\Models\Repositories\PostRepository;
use App\Models\Repositories\PostRepositoryInterface;
use App\Models\Repositories\TagRepository;
use App\Models\Repositories\TagRepositoryInterface;
use App\Models\Repositories\UserRepository;
use App\Models\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Container as SlimContainer;
use Dotenv\Dotenv;
use PDO;


/**
 * Class ConsoleBootstrap
 * @package App\Console\DB
 */
class ConsoleBootstrap
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * ConsoleBootstrap constructor.
     */
    public function __construct()
    {
        $this->loadEnv();
        $this->container = new SlimContainer();
        $this->bindDependencies();
        $this->bindCreateSqlScripts();
    }

    /**
     * Get PDO
     * @return PDO
     */
    private function getPDO()
    {
        $host = getenv('DB_HOST');
        $db = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $user, $pass, $opt);
    }

    /**
     * Load config .env
     */
    private function loadEnv()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../../../');
        $dotenv->overload();
        $dotenv->required([
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD'
        ]);
    }

    /**
     * Bind dependencies to container
     */
    private function bindDependencies()
    {
        $this->container['devMode'] = false;
        $this->container['pdo'] = $this->getPDO();
        $this->container[CategoryRepositoryInterface::class] = function ($c) {
            return new CategoryRepository($c);
        };
        $this->container[UserRepositoryInterface::class] = function ($c) {
            return new UserRepository($c);
        };
        $this->container[CategoryRepositoryInterface::class] = function ($c) {
            return new CategoryRepository($c);
        };
        $this->container[PostRepositoryInterface::class] = function ($c) {
            return new PostRepository($c);
        };
        $this->container[TagRepositoryInterface::class] = function ($c) {
            return new TagRepository($c);
        };
    }

    /**
     *  Bind SQL scripts to container
     */
    private function bindCreateSqlScripts()
    {
        $this->container['users'] = "CREATE TABLE `users`
            (
                `id`       int(11)      NOT NULL AUTO_INCREMENT,
                `email`    varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `name`     varchar(100) DEFAULT NULL,
                `avatar`   varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_uindex` (`email`)
            ) ENGINE = InnoDB
              DEFAULT CHARSET = UTF8;";

        $this->container['categories'] = "CREATE TABLE `categories`
            (
            `id`          int(11)      NOT NULL AUTO_INCREMENT,
            `name`        varchar(255) NOT NULL,
            `image`       varchar(255) DEFAULT NULL,
            `description` longtext,
            PRIMARY KEY (`id`),
            UNIQUE KEY `categories_name_uindex` (`name`)
            ) ENGINE = InnoDB
            DEFAULT CHARSET = UTF8;";

        $this->container['posts'] = "CREATE TABLE `posts`
            (
                `id`           int(11)      NOT NULL AUTO_INCREMENT,
                `title`        varchar(255) NOT NULL,
                `slug`         varchar(100) NOT NULL,
                `image`        varchar(255) DEFAULT NULL,
                `description`  longtext     NOT NULL,
                `body`         longtext     NOT NULL,
                `created_at`   datetime     NOT NULL,
                `updated_at`   datetime     NOT NULL,
                `published_at` datetime     NOT NULL,
                `published`    bool         NOT NULL,
                `category_id`  int(11)      NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `posts_slug_uindex` (`slug`),
                KEY `posts_categories_id_fk` (`category_id`),
                CONSTRAINT `posts_categories_id_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
            ) ENGINE = InnoDB
              DEFAULT CHARSET = UTF8;";

        $this->container['tags'] = "CREATE TABLE `tags`
            (
                `id`    int(11)      NOT NULL AUTO_INCREMENT,
                `title` varchar(100) NOT NULL,
                `slug`  varchar(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `tags_slug_uindex` (`slug`),
                UNIQUE KEY `tags_title_uindex` (`title`)
            ) ENGINE = InnoDB
              AUTO_INCREMENT = 2
              DEFAULT CHARSET = UTF8;";
    }
}
