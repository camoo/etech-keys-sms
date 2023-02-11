<?php

namespace Etech\Sms\Interfaces;

interface Drivers
{
    public static function getInstance(array $options = []);

    public function insert(string $table, array $variables = []);

    public function close();
}
