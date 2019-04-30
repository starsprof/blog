<?php


namespace App\Console\DB;

use Exception;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * Class Table
 * @package App\Console\DB
 */
class Table
{
    /**
     * @var PDO
     */
    protected $pdo;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Table constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pdo = $this->container->get('pdo');
    }

    /**
     * Create new table in DB
     * @param string $tableName
     * @param bool $interactive
     */
    public function createTable(string $tableName, bool $interactive = true)
    {
        $response = '';
        if ($interactive) {
            do {
                echo "\033[1;35mCreate `$tableName` table (dropped if exist)? (y/n) - \033[0m";
                $stdin = fopen('php://stdin', 'r');
                $response = fgetc($stdin);
            } while (!in_array($response, ['y', 'n']));
        }
        if ($response == 'y' || !$interactive) {
            try {

                $this->pdo->beginTransaction();
                $this->pdo->exec('SET foreign_key_checks = 0');
                $this->pdo->exec("DROP TABLE IF EXISTS  `$tableName`");
                $this->pdo->exec($this->container->get($tableName));
                $this->pdo->exec('SET foreign_key_checks = 1');
                $this->pdo->commit();

            } catch (Exception $exception) {
                $this->pdo->rollBack();
                echo "\n Ошибка: " . $exception->getMessage() . "\n";
                die();
            }
            echo "Table `$tableName` created\n";
        }

    }

    /**
     * Add fake entities to DB
     * @param string $name
     * @param array $entities
     * @param string $repositoryInterface
     * @param bool $interactive
     */
    public function addFakeEntity(
        string $name,
        array $entities,
        string $repositoryInterface,
        bool $interactive = true
    )
    {
        $repository = $this->container->get($repositoryInterface);
        $response = '';
        if ($interactive) {
            do {
                echo "\033[1;35mAdd fake `$name`? (y/n) - \033[0m";
                $stdin = fopen('php://stdin', 'r');
                $response = fgetc($stdin);
            } while (!in_array($response, ['y', 'n']));
        }
        if ($response == 'y' || !$interactive) {
            try {
                foreach ($entities as $entity) {
                    $repository->create($entity);
                }
            } catch (Exception $exception) {
                echo "\n Ошибка: " . $exception->getMessage() . "\n";
                die();
            }
            echo count($entities) . " `$name` inserted\n";
        }
    }
}