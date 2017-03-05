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
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8');

    $pdo->exec('CREATE TABLE chat_sequences (
        "user_id" INT "users" NOT NULL UNIQUE,
        "message_id" INT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8');
}
