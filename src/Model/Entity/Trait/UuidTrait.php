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

    public function setUuid(Uuid|string|null $uuid = null): self
    {
        if ($uuid) {
            if ($uuid instanceof Uuid) {
                $this->uuid = $uuid;
            } else {
                $this->uuid = Uuid::fromBytes($uuid);
            }
        } else {
            $this->uuid = Uuid::uuid7();
        }

        return $this;
    }
}
