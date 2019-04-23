<?php


namespace App\Core\Utils;


use PDO;
use PDOStatement;

class LoggedPDO extends PDO
{
    public function __construct($dsn, $username = null, $passwd = null, $options = null)
    {
        parent::__construct($dsn, $username, $passwd, $options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array (LoggedPDOStatement::class, [&$this]));
    }

    public function log(string $query, float $time, array $args = null)
    {
        $_SESSION['db_log'][] = ['args' => $args, 'query' =>$query, 'time' => round($time*1000,2)];

    }
    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        $start = microtime(true);
        $result = parent::query($statement);
        $this->log($statement, microtime(true) - $start);
        return $result;
    }
}