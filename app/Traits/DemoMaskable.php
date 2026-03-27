<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait DemoMaskable
{
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->shouldMask() ? $this->maskEmail($value) : $value,
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->shouldMask() ? $this->maskPhone($value) : $value,
        );
    }

    protected function contactPersonNumber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->shouldMask() ? $this->maskPhone($value) : $value,
        );
    }

    protected function shouldMask(): bool
    {
        return getEnvMode() === 'demo' && !request()->is('api/*');
    }

    protected function maskEmail(?string $email): ?string
    {
        if (!$email || !str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);

        return substr($name, 0, 1)
            . str_repeat('*', min(10, strlen($name) - 1))
            . '@' . $domain;
    }

    protected function maskPhone(?string $phone): ?string
    {
        if (!$phone || strlen($phone) < 10) {
            return $phone;
        }

        return substr($phone, 0, 1)
            . str_repeat('*', min(10, strlen($phone) - 1));
    }
}
