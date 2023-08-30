<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Interfaces;

interface FactoryInterface
{
    public function createEntity(array $data): EntityInterface;

    public function createCollection(array $data): CollectionInterface;
}
