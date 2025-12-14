<?php

namespace App\Domain\Shared\Service;

interface PasswordHasher
{
    public function hash(string $plain): string;
}
