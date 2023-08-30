<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Base;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use Tymeshift\PhpTest\Exceptions\InvalidCollectionDataProvidedException;
use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\EntityInterface;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;

abstract class BaseCollection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable, CollectionInterface
{
    /** @var EntityInterface[] */
    protected array $items = [];

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $item) {
            if ($this->isEntity($item)) {
                $this->items[$key] = $item;
            } else {
                throw new InvalidCollectionDataProvidedException();
            }
        }
    }

    public function add(EntityInterface $entity): CollectionInterface
    {
        $this->items[] = $entity;
        return $this;
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     * @see buildFromArray
     */
    public function createFromArray(array $data, FactoryInterface $factory): CollectionInterface
    {
        foreach ($data as $item) {
            if (is_array($item)) {
                $this->items[] = $factory->createEntity($item);
            } else {
                throw new InvalidCollectionDataProvidedException();
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Searches for an element. $key will be transformed into getKey method and result of it will be compared to value
     * Comparison is strict
     */
    public function search(mixed $key, bool $value): ?EntityInterface
    {
        foreach ($this->items as $item) {
            if ($item->{'get' . $key}() === $value) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Remove item from collection
     */
    public function remove(string $key, mixed $value): ?bool
    {
        foreach ($this->items as $index => $item) {
            if ($item->{'get' . $key}() === $value) {
                unset($this->items[$index]);
                return true;
            }
        }
        return null;
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);

        $newItems = [];
        foreach ($this->items as $value) {
            $clonedValue = clone $value;
            $newItems[] = $callback($clonedValue);
        }

        return new static(array_combine($keys, $newItems));
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function filter(callable $callback): static
    {
        $newItems = [];
        foreach ($this->items as $value) {
            $clonedValue = clone $value;
            if ($callback($clonedValue)) {
                $newItems[] = $clonedValue;
            }
        }

        return new static($newItems);
    }

    public function getIterator(): Traversable|ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     */
    public function offsetExists(mixed $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Get an item at a given offset.
     */
    public function offsetGet(mixed $key): EntityInterface
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @throws InvalidCollectionDataProvidedException
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (!$this->isEntity($value)) {
            throw new InvalidCollectionDataProvidedException();
        }
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     */
    public function offsetUnset(mixed $key): void
    {
        unset($this->items[$key]);
    }

    protected function isEntity(mixed $item): bool
    {
        return ($item instanceof EntityInterface);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Execute a callback over each item.
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function buildFromArray(array $data, FactoryInterfaceAlias $factory): self
    {
        foreach ($data as $item) {
            if (is_array($item)) {
                $this->items[] = $factory->build($item);
            } else {
                throw new InvalidCollectionDataProvidedException();
            }
        }

        return $this;
    }

    public function getById(mixed $id): ?EntityInterface
    {
        foreach ($this->items as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }
        return null;
    }

    public function pluck(string $property): array
    {
        $data = [];
        foreach ($this->items as $entity) {
            $data[] = $entity->{'get' . ucfirst($property)}();
        }
        return $data;
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function getAssoc(string $property = 'id'): CollectionInterface
    {
        $items = [];
        foreach ($this->items as $index => $entity) {
            $newKey = $entity->{'get' . ucfirst($property)}();
            $items[$newKey] = $this->items[$index];
        }

        return new static($items);
    }

    public function getIds(): array
    {
        $result = [];
        foreach ($this->items as $entity) {
            $result[] = $entity->getId();
        }
        return array_unique($result);
    }

    public function last(): EntityInterface
    {
        $lastItem = end($this->items);
        reset($this->items);
        return $lastItem;
    }
}
