<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Components;

interface DatabaseInterface
{
    public function query(string $query, array $params):array;
}
