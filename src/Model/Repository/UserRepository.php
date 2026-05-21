<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\PasswordHasher;
use PDO;
use Ramsey\Uuid\Uuid;

class UserRepository extends BaseRepository
{
    // Repository pracuje jen s entitou User a skrývá SQL detaily před controllery.
    public function findByEmail(string $email): User|null
    {
        $sql = "SELECT * FROM `user` WHERE email = :email";

        $statement = $this->dbConnection->prepare($sql);
        $statement->bindParam("email", $email);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0] ?? null;

        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    public function findByUsername(string $username): User|null
    {
        $sql = "SELECT * FROM `user` WHERE username = :username";

        $statement = $this->dbConnection->prepare($sql);
        $statement->bindParam("username", $username);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0] ?? null;

        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    public function findById(Uuid|string $id): User|null
    {
        $uuid = $this->convertUuid($id);

        $sql = "SELECT * FROM `user` WHERE uuid = :uuid LIMIT 1";

        $statement = $this->dbConnection->prepare($sql);
        $statement->bindValue('uuid', $uuid->getBytes(), PDO::PARAM_LOB);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0] ?? null;

        if (!$row) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    /** @return array<int, User> */
    public function findAllExcept(Uuid|string $userId): array
    {
        $uuid = $this->convertUuid($userId);

        $sql = "SELECT * FROM `user` WHERE uuid <> :uuid ORDER BY username ASC";
        $statement = $this->dbConnection->prepare($sql);
        $statement->bindValue('uuid', $uuid->getBytes(), PDO::PARAM_LOB);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    public function save(User $user): void
    {
        $passwordHasher = new PasswordHasher();

        if ($user->getUuid() === null) {
            // insert
            $sql = <<<SQL
                INSERT INTO `user` 
                    (
                     `uuid`,
                     `username`,
                     `email`,
                     `password`,
                     `phone`,
                     `first_name`,
                     `last_name`,
                     `gender`,
                     `birthdate`
                     )
                VALUES (
                        :uuid,
                        :username,
                        :email,
                        :password,
                        :phone,
                        :first_name,
                        :last_name,
                        :gender,
                        :birthdate
                )
        SQL;

            $passwordHash = $passwordHasher->hash($user->getPassword());

            $stm = $this->dbConnection->prepare($sql);
            $stm->bindValue('uuid', Uuid::uuid7()->getBytes());
            $stm->bindValue('username', $user->getUsername());
            $stm->bindValue('email', $user->getEmail());
            $stm->bindValue('password', $passwordHash);
            $stm->bindValue('phone', $user->getPhone());
            $stm->bindValue('first_name', $user->getFirstName());
            $stm->bindValue('last_name', $user->getLastName());
            $stm->bindValue('gender', $user->getGender()->value);

            $birthdateString = $user->getBirthDate()->format('Y-m-d') ?? null;
            $stm->bindValue('birthdate', $birthdateString);
        } else {
            $sql = <<<SQL
                UPDATE `user` SET
                    `username` = :username,
                    `email` = :email,
                    `password` = :password,
                    `phone` = :phone,
                    `first_name` = :firstName,
                    `last_name` = :lastName,
                    `gender` = :gender,
                    `birthdate` = :birthdate,
                    `updated_at` = NOW()
                WHERE `user`.`uuid` = :uuid
SQL;

            $stm = $this->dbConnection->prepare($sql);
            $stm->bindValue('username', $user->getUsername());
            $stm->bindValue('email', $user->getEmail());
            $stm->bindValue('password', $user->getPassword());
            $stm->bindValue('phone', $user->getPhone());
            $stm->bindValue('firstName', $user->getFirstName());
            $stm->bindValue('lastName', $user->getLastName());
            $stm->bindValue('gender', $user->getGender()->value);
            $birthdateString = $user->getBirthDate()->format('Y-m-d') ?? null;
            $stm->bindValue('birthdate', $birthdateString);
            $stm->bindValue('uuid', $user->getUuid()->getBytes());
        }

        $stm->execute();
    }

    private function mapRowToUser(array $row): User
    {
        $user = new User(
            $row['username'],
            $row['email'],
            $row['password'],
            $row['first_name'],
        );
        $user->setPhone($row['phone'] !== null ? (int)$row['phone'] : null)
            ->setLastName($row['last_name'])
            ->setGender($row['gender'])
            ->setBirthDate($row['birthdate'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
            ->setUuid($row['uuid']);

        return $user;
    }
}
