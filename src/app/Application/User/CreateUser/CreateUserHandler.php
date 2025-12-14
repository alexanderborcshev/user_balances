<?php

namespace App\Application\User\CreateUser;

use App\Domain\Shared\Service\PasswordHasher;
use App\Domain\User\User;
use App\Domain\User\UserRepository;

readonly class CreateUserHandler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
    ) {
    }

    public function handle(CreateUserCommand $command): User
    {
        $user = User::create(
            $command->name,
            $command->email,
            $this->hasher->hash($command->password)
        );

        return $this->users->create($user);
    }
}
