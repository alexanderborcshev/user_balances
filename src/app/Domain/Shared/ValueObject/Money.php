<?php

namespace App\Domain\Shared\ValueObject;

use InvalidArgumentException;

class Money
{
    private string $amount;

    private function __construct(string $amount)
    {
        $this->amount = $amount;
    }

    /**
    * @throws InvalidArgumentException
     */
    public static function fromNumeric(float|string $value): self
    {
        if (! is_numeric($value)) {
            throw new InvalidArgumentException('Money value must be numeric.');
        }

        $normalized = number_format((float) $value, 2, '.', '');

        return new self($normalized);
    }

    public function add(self $other): self
    {
        $sum = (float) $this->amount + (float) $other->amount;

        return new self(number_format($sum, 2, '.', ''));
    }

    public function isZero(): bool
    {
        return $this->amount === '0.00';
    }

    public function isNegative(): bool
    {
        return (float) $this->amount < 0;
    }

    public function format(): string
    {
        return $this->amount;
    }
}
