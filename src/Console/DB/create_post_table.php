<?php

require_once __DIR__.'/../../../vendor/autoload.php';

$pdo = \App\Console\DB\ConsolePDO::getPDO();

try {
    $pdo->beginTransaction();
    $pdo->exec('DROP TABLE IF EXISTS  `users`');
    $sql = "CREATE TABLE `posts`
            (
                `id`           int(11)      NOT NULL AUTO_INCREMENT,
                `title`        varchar(255) NOT NULL,
                `slug`         varchar(100)  NOT NULL,
                `image`        varchar(255) DEFAULT NULL,
                `description`  longtext     NOT NULL,
                `created_at`   datetime     NOT NULL,
                `updated_at`   datetime     NOT NULL,
                `published_at` datetime     DEFAULT NULL,
                `published`    bool   NOT NULL,
                `category_id`  int(11)      NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `posts_slug_uindex` (`slug`),
                KEY `posts_categories_id_fk` (`category_id`),
                CONSTRAINT `posts_categories_id_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
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