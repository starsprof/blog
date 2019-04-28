<?php

require_once __DIR__.'/../../../vendor/autoload.php';

$pdo = \App\Console\DB\ConsolePDO::getPDO();

try {
    $pdo->beginTransaction();
    $pdo->exec('DROP TABLE IF EXISTS  `tags`');
    $sql = "CREATE TABLE `tags`
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
    $pdo->exec($sql);
    $pdo->commit();
}
catch (Exception $e) {
    $pdo->rollBack();
    echo "Ошибка: " . $e->getMessage();
    die();
}