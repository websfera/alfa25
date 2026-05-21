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

    protected function convertUuid(UuidInterface|string $uuid): UuidInterface
    {
        if ($uuid instanceof UuidInterface) {
            return $uuid;
        }

        // Z DB může přijít binární UUID (BINARY(16)), z URL naopak textový UUID string.
        if (strlen($uuid) === 16) {
            return Uuid::fromBytes($uuid);
        }

        return Uuid::fromString($uuid);
    }
}
