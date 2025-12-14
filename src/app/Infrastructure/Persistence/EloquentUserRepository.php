<?php

namespace App\Infrastructure\Persistence;

use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User as DomainUser;
use App\Domain\User\UserRepository;
use App\Models\User as EloquentUser;

class EloquentUserRepository implements UserRepository
{
    public function create(DomainUser $user): DomainUser
    {
        $eloquent = new EloquentUser();
        $eloquent->name = $user->getName();
        $eloquent->email = $user->getEmail();
        $eloquent->password = $user->getPasswordHash();
        $eloquent->save();

        return $this->toDomain($eloquent);
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $eloquent = EloquentUser::where('email', $email)->first();

        return $eloquent ? $this->toDomain($eloquent) : null;
    }

    public function getById(int $id): DomainUser
    {
        $eloquent = EloquentUser::find($id);

        if (! $eloquent) {
            throw new UserNotFoundException("User $id not found");
        }

        return $this->toDomain($eloquent);
    }

    public function getByIdWithLock(int $id): DomainUser
    {
        $eloquent = EloquentUser::lockForUpdate()->find($id);

        if (! $eloquent) {
            throw new UserNotFoundException("User $id not found");
        }

        return $this->toDomain($eloquent);
    }

    private function toDomain(EloquentUser $eloquent): DomainUser
    {
        return new DomainUser(
            $eloquent->id,
            $eloquent->name,
            $eloquent->email,
            $eloquent->password,
        );
    }
}
