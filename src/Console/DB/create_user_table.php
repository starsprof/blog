<?php

require_once __DIR__.'/../../../vendor/autoload.php';

$pdo = \App\Console\DB\ConsolePDO::getPDO();

try {
    $pdo->beginTransaction();
    $pdo->exec('DROP TABLE IF EXISTS  `users`');
    $sql = "CREATE TABLE `users`
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
    $pdo->exec($sql);
    $pdo->commit();
}
catch (Exception $e) {
    $pdo->rollBack();
        echo "Ошибка: " . $e->getMessage();
        die();
}