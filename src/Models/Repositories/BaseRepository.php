<?php


namespace App\Models\Repositories;


use App\Core\Utils\LoggedPDO;
use PDO;
use Psr\Container\ContainerInterface;

abstract class BaseRepository
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var ContainerInterface
     */
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pdo = self::getPDO($this->container->get('devMode'));
    }

    public static function getPDO(bool $isDevMode)
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
        if($isDevMode) {
            return new LoggedPDO($dsn, $user, $pass, $opt);
        }
        return new PDO($dsn, $user, $pass, $opt);
    }
}