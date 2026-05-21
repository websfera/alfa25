<?php

declare(strict_types=1);

namespace App\Model\Entity\Trait;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait
{
    protected UuidInterface|null $uuid;

    public function getUuid(): UuidInterface|null
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface|string|null $uuid = null): self
    {
        if ($uuid) {
            if ($uuid instanceof UuidInterface) {
                $this->uuid = $uuid;
            } else {
                // String může být buď canonical UUID (36 znaků), nebo binární UUID (16 bajtů).
                $this->uuid = strlen($uuid) === 16
                    ? Uuid::fromBytes($uuid)
                    : Uuid::fromString($uuid);
            }
        } else {
            $this->uuid = Uuid::uuid7();
        }

        return $this;
    }
}
