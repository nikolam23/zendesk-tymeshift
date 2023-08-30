<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Interfaces;

interface StorageInterface
{
    public function getById(int $id): array;

    public function getByIds(array $ids): array;
}
