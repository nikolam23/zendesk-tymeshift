<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule;

use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;
use Tymeshift\PhpTest\Interfaces\RepositoryInterface;
use Tymeshift\PhpTest\Interfaces\StorageInterface;

final class ScheduleRepository implements RepositoryInterface
{
    public function __construct(private StorageInterface $storage, private FactoryInterface $factory)
    {
    }

    public function getById(int $id): ScheduleEntityInterface
    {
        $data = $this->storage->getById($id);

        return $this->factory->createEntity($data);
    }

    public function getByIds(array $ids): CollectionInterface
    {
        $data = $this->storage->getByIds($ids);

        return $this->factory->createCollection($data);
    }
}
