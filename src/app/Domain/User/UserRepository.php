<?php

namespace App\Domain\User;

interface UserRepository
{
    public function create(User $user): User;

    public function findByEmail(string $email): ?User;

    public function getById(int $id): User;

    public function getByIdWithLock(int $id): User;
}
