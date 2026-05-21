<?php

namespace App\Model\Entity\Trait;

use DateTime;

trait CreatedAtTrait
{
    protected DateTime $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime|string $createdAt): self
    {
        if (!$createdAt instanceof DateTime) {
            $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);
        }

        $this->createdAt = $createdAt;

        return $this;
    }
}
