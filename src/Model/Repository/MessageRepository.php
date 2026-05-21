<?php

namespace App\Model\Repository;

use App\Model\Database;
use App\Model\Entity\Message;

class MessageRepository extends BaseRepository
{
    protected UserRepository $userRepository;
    protected ConversationRepository $conversationRepository;

    public function __construct(
        Database $dbConnection,
        UserRepository $userRepository,
        ConversationRepository $conversationRepository
    )
    {
        parent::__construct($dbConnection);

        $this->userRepository = $userRepository;
        $this->conversationRepository = $conversationRepository;
    }

    protected function mapRowToMessage(array $row): Message
    {
        $message = new Message();
        $message->setUuid($row['uuid']);
        $message->setMessage($row['message']);
        $message->setCreatedAt($row['created_at']);

        // fetch user entity
        $user = $this->userRepository->findById($row['user_uuid']);
        $message->setUser($user);

        // fetch conversation entity
        $conversation = $this->conversationRepository->findById($row['conversation_uuid']);
        $message->setConversation($conversation);

        return $message;
    }
}
