<?php

namespace Kanboard\Plugin\Chat\Schema;

use PDO;

const VERSION = 1;

function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE chat_messages (
        "id" SERIAL PRIMARY KEY,
        "message" TEXT NOT NULL,
        "user_id" INTEGER NOT NULL,
        "creation_date" INTEGER NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE chat_users (
        "user_id" INTEGER NOT NULL UNIQUE,
        "message_id" INTEGER DEFAULT 0,
        "mentioned" BOOLEAN DEFAULT \'0\',
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');
}
