<?php


namespace App\Models\Repositories;


use PDO;

abstract class BaseRepository
{
    /**
     * @var PDO
     */
    protected $pdo;

    public function __construct()
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
        $this->pdo = new PDO($dsn, $user, $pass, $opt);
    }

    public abstract function findOneById($id);

    public abstract function findManyByIds($ids = array());

    public abstract function findAll();

    public abstract function deleteOneById($id);
}