<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Database;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class BaseRepository
{
    protected Database $dbConnection;

    public function __construct(Database $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    protected function convertUuid(Uuid|string $uuid): UuidInterface
    {
        if ($uuid instanceof Uuid) {
            return $uuid;
        }

        return Uuid::fromString($uuid);
    }
}
