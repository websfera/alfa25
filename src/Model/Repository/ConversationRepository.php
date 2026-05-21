<?php

namespace App\Model\Repository;

use App\Model\Entity\Conversation;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ConversationRepository extends BaseRepository
{
    public function findById(Uuid|string $id): Conversation|null
    {
        $uuid = $this->convertUuid($id);

        $sql = 'SELECT * FROM conversation WHERE uuid = :uuid';
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch();

        if ($row) {
            return $this->mapRowToConversation($row);
        }

        return null;
    }

    /** @return array<int, Conversation> */
    public function findAllByUser(Uuid|string $userId): array
    {
        $userUuid = $this->convertUuid($userId);

        $sql = <<<SQL
SELECT * FROM conversation
LEFT JOIN conversation_member ON conversation.uuid = conversation_member.conversation_uuid
LEFT JOIN user ON conversation_member.user_uuid = user.uuid
WHERE user.uuid = :uuid
SQL;
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute(['uuid' => $userUuid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) === 0) {
            return [];
        }

        $conversations = [];
        foreach ($rows as $row) {
            $conversations[] = $this->mapRowToConversation($row);
        }

        return $conversations;
    }

    private function mapRowToConversation(array $row): Conversation
    {
        $conversation = new Conversation();
        $conversation->setUuid($row['uuid']);
        $conversation->setName($row['name']);
        $conversation->setIsGroup((bool)$row['is_group']);
        $conversation->setCreatedAt($row['created_at']);

        return $conversation;
    }
}
