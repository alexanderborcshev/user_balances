<?php

namespace App\Domain\User;

use InvalidArgumentException;

readonly class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $passwordHash,
    ) {
        if ($name === '') {
            throw new InvalidArgumentException('Name must not be empty.');
        }

        if ($email === '') {
            throw new InvalidArgumentException('Email must not be empty.');
        }

        if ($passwordHash === '') {
            throw new InvalidArgumentException('Password hash must not be empty.');
        }
    }

    public static function create(string $name, string $email, string $passwordHash): self
    {
        return new self(null, $name, $email, $passwordHash);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

}
