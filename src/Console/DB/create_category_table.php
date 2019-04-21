<?php


require_once __DIR__.'/../../../vendor/autoload.php';

$pdo = \App\Console\DB\ConsolePDO::getPDO();

try {
    $pdo->beginTransaction();
    $pdo->exec('DROP TABLE IF EXISTS  `categories`');
    $sql = "CREATE TABLE `categories`
            (
            `id`          int(11)      NOT NULL AUTO_INCREMENT,
            `name`        varchar(255) NOT NULL,
            `image`       varchar(255) DEFAULT NULL,
            `description` longtext,
            PRIMARY KEY (`id`),
            UNIQUE KEY `categories_name_uindex` (`name`)
            ) ENGINE = InnoDB
            DEFAULT CHARSET = UTF8;";
    $pdo->exec($sql);
    $pdo->commit();
}
catch (Exception $e) {
    $pdo->rollBack();
    echo "Ошибка: " . $e->getMessage();
    die();
}

