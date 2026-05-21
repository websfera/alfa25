<?php

namespace App\Model\Repository;

use App\Model\Database;
use App\Model\Entity\Message;
use PDO;
use Ramsey\Uuid\Uuid;

class MessageRepository extends BaseRepository
{
    // Repository drží SQL nad zprávami; mapování na entity je centralizované zde.
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

    /** @return array<int, Message> */
    public function findAllByConversation(Uuid|string $conversationId, int $limit = 50, int $offset = 0): array
    {
        $conversationUuid = $this->convertUuid($conversationId);

        $sql = <<<SQL
SELECT uuid, message, created_at, user_uuid, conversation_uuid
FROM message
WHERE conversation_uuid = :conversation_uuid
ORDER BY created_at ASC
LIMIT :limit OFFSET :offset
SQL;

        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue('conversation_uuid', $conversationUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $messages = [];

        foreach ($rows as $row) {
            $messages[] = $this->mapRowToMessage($row);
        }

        return $messages;
    }

    public function create(Uuid|string $conversationId, Uuid|string $userId, string $text): Message
    {
        $conversationUuid = $this->convertUuid($conversationId);
        $userUuid = $this->convertUuid($userId);
        $messageUuid = Uuid::uuid7();

        $sql = <<<SQL
INSERT INTO message (uuid, message, user_uuid, conversation_uuid)
VALUES (:uuid, :message, :user_uuid, :conversation_uuid)
SQL;

        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue('uuid', $messageUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->bindValue('message', $text);
        $stmt->bindValue('user_uuid', $userUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->bindValue('conversation_uuid', $conversationUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->execute();

        return $this->mapRowToMessage([
            'uuid' => $messageUuid->getBytes(),
            'message' => $text,
            'created_at' => date('Y-m-d H:i:s'),
            'user_uuid' => $userUuid->getBytes(),
            'conversation_uuid' => $conversationUuid->getBytes(),
        ]);
    }
}
