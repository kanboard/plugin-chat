<?php

namespace Kanboard\Plugin\Chat\Schema;

use PDO;

const VERSION = 1;

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
