<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Interfaces;

interface CollectionInterface
{
    /**
     * Adds item to collection
     */
    public function add(EntityInterface $entity):self;

    /**
     * Creates Collection from array
     */
    public function createFromArray(array $data, FactoryInterface $factory):self;

    /**
     * Creates array from collection
     */
    public function toArray():array;
}
