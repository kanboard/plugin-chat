<?php

namespace Kanboard\Plugin\Chat\Schema;

use PDO;

const VERSION = 2;

function version_2(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `chat_messages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('ALTER TABLE `chat_users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}

function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE chat_messages (
        `id` INT NOT NULL AUTO_INCREMENT,
        `message` TEXT NOT NULL,
        `user_id` INT NOT NULL,
        `creation_date` INT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8');

    $pdo->exec('CREATE TABLE chat_users (
        `user_id` INT NOT NULL UNIQUE,
        `message_id` INT DEFAULT 0,
        `mentioned` TINYINT(1) DEFAULT 0,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8');
}
