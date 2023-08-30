<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\EntityInterface;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;
use Tymeshift\PhpTest\Interfaces\RepositoryInterface;

final class TaskRepository implements TaskRepositoryInterface, RepositoryInterface
{
    public function __construct(private TaskStorageInterface $storage, private FactoryInterface $factory)
    {
    }

    public function getById(int $id): EntityInterface
    {
        $data = $this->storage->getById($id);

        return $this->factory->createEntity($data);
    }

    public function getByScheduleId(int $scheduleId): CollectionInterface
    {
        $data = $this->storage->getByScheduleId($scheduleId);

        return $this->factory->createCollection($data);
    }

    public function getByIds(array $ids): CollectionInterface
    {
        $data = $this->storage->getByIds($ids);

        return $this->factory->createCollection($data);
    }
}
