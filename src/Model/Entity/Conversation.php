<?php

namespace App\Model\Entity;

use App\Model\Entity\Trait\CreatedAtTrait;

class Conversation
{
    use Trait\UuidTrait, CreatedAtTrait;

    protected string $name;
    protected bool $isGroup;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Conversation
    {
        $this->name = $name;
        return $this;
    }

    public function isGroup(): bool
    {
        return $this->isGroup;
    }

    public function setIsGroup(bool $isGroup): Conversation
    {
        $this->isGroup = $isGroup;
        return $this;
    }

}
