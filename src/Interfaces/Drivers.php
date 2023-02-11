<?php

declare(strict_types=1);

namespace Etech\Sms\Interfaces;

interface Drivers
{
    public static function getInstance(array $options = []): self;

    public function insert(string $table, array $variables = []): bool;

    public function close(): bool;
}
