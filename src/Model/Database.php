<?php

namespace App\Model;

use PDO;

class Database extends PDO
{
    public function __construct(
        string $dsn,
        string|null $username = null,
        string|null $password = null,
        array|null $options = null,
    ) {
        parent::__construct($dsn, $username, $password, $options);

        $this->exec("SET NAMES 'utf8'");
        $this->exec("SET CHARACTER SET utf8");
        $this->exec("SET CHARACTER_SET_CONNECTION=utf8");
        $this->exec("SET SQL_MODE=''");
    }
}
