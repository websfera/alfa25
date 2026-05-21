<?php

namespace App\Model\Entity;

use App\Model\Entity\Trait\CreatedAtTrait;
use App\Model\Entity\Trait\UuidTrait;

class Message
{
    use UuidTrait;
    use CreatedAtTrait;

    protected string $message;
    protected User $user;
    protected Conversation $conversation;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Message
    {
        $this->message = $message;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Message
    {
        $this->user = $user;
        return $this;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): Message
    {
        $this->conversation = $conversation;
        return $this;
    }
}
