<?php

namespace App\Model\Entity;

use App\Enum\GenderEnum;
use DateTime;

class User
{
    use Trait\UuidTrait;
    use Trait\CreatedAtTrait;
    protected string $username;
    protected string $email;
    protected string $password;
    protected int|null $phone;
    protected string $firstName;
    protected string|null $lastName;
    protected GenderEnum|null $gender;
    protected DateTime|null $birthDate;
    protected DateTime|null $updatedAt;

    public function __construct(
        string $username,
        string $email,
        string $password,
        string $firstName,
        int|null $phone = null,
        string|null $lastName = null,
        GenderEnum|null $gender = null,
        DateTime|null $birthDate = null,
        DateTime|null $updatedAt = null,
    ) {
        $this->uuid = null;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
        $this->createdAt = new DateTime();
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime|string|null $updatedAt): User
    {
        if ($updatedAt && !$updatedAt instanceof DateTime) {
            $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $updatedAt);
        }

        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTime|string|null $birthDate): User
    {
        if ($birthDate && !$birthDate instanceof DateTime) {
            $birthDate = DateTime::createFromFormat('Y-m-d', $birthDate);
        }

        $this->birthDate = $birthDate;

        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(GenderEnum|string|null $gender): User
    {
        if ($gender !== null && !$gender instanceof GenderEnum) {
            $gender = GenderEnum::tryFrom($gender);
        }

        $this->gender = $gender;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): User
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }
}
