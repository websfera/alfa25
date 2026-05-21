<?php

namespace App\Model\Repository;

use App\Model\Entity\Conversation;
use PDO;
use Ramsey\Uuid\Uuid;

class ConversationRepository extends BaseRepository
{
    // Repository řeší dotazy nad konverzacemi; controller dostává hotové entity.
    public function findById(Uuid|string $id): Conversation|null
    {
        $uuid = $this->convertUuid($id);

        $sql = 'SELECT * FROM conversation WHERE uuid = :uuid LIMIT 1';
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue('uuid', $uuid->getBytes(), PDO::PARAM_LOB);
        $stmt->execute();
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
SELECT c.* FROM conversation c
INNER JOIN conversation_member cm ON c.uuid = cm.conversation_uuid
WHERE cm.user_uuid = :uuid
ORDER BY c.created_at DESC
SQL;
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue('uuid', $userUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->execute();
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

    public function findDirectConversationBetweenUsers(Uuid|string $firstUserId, Uuid|string $secondUserId): Conversation|null
    {
        $firstUuid = $this->convertUuid($firstUserId);
        $secondUuid = $this->convertUuid($secondUserId);

        $sql = <<<SQL
SELECT c.* FROM conversation c
INNER JOIN conversation_member cm1 ON cm1.conversation_uuid = c.uuid
INNER JOIN conversation_member cm2 ON cm2.conversation_uuid = c.uuid
WHERE c.is_group = 0
  AND cm1.user_uuid = :first_user
  AND cm2.user_uuid = :second_user
LIMIT 1
SQL;

        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue('first_user', $firstUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->bindValue('second_user', $secondUuid->getBytes(), PDO::PARAM_LOB);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapRowToConversation($row);
    }

    public function createDirectConversation(
        Uuid|string $firstUserId,
        Uuid|string $secondUserId,
        ?string $conversationName = null,
    ): Conversation {
        $firstUuid = $this->convertUuid($firstUserId);
        $secondUuid = $this->convertUuid($secondUserId);
        $conversationUuid = Uuid::uuid7();

        // Kritická část: vytváříme konverzaci i členství atomicky v jedné transakci.
        $this->dbConnection->beginTransaction();

        try {
            $conversationSql = <<<SQL
INSERT INTO conversation (uuid, name, is_group)
VALUES (:uuid, :name, 0)
SQL;

            $conversationStmt = $this->dbConnection->prepare($conversationSql);
            $conversationStmt->bindValue('uuid', $conversationUuid->getBytes(), PDO::PARAM_LOB);
            $conversationStmt->bindValue('name', $conversationName);
            $conversationStmt->execute();

            $memberSql = <<<SQL
INSERT INTO conversation_member (conversation_uuid, user_uuid)
VALUES (:conversation_uuid, :user_uuid)
SQL;

            $memberStmt = $this->dbConnection->prepare($memberSql);
            $memberStmt->bindValue('conversation_uuid', $conversationUuid->getBytes(), PDO::PARAM_LOB);

            $memberStmt->bindValue('user_uuid', $firstUuid->getBytes(), PDO::PARAM_LOB);
            $memberStmt->execute();

            $memberStmt->bindValue('user_uuid', $secondUuid->getBytes(), PDO::PARAM_LOB);
            $memberStmt->execute();

            $this->dbConnection->commit();
        } catch (\Throwable $e) {
            $this->dbConnection->rollBack();
            throw $e;
        }

        $conversation = new Conversation();
        $conversation->setUuid($conversationUuid)
            ->setName($conversationName ?? 'Konverzace')
            ->setIsGroup(false)
            ->setCreatedAt(date('Y-m-d H:i:s'));

        return $conversation;
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
