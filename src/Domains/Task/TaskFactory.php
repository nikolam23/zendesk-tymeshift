<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

use Tymeshift\PhpTest\Exceptions\InvalidCollectionDataProvidedException;
use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\EntityInterface;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;

final class TaskFactory implements FactoryInterface
{
    public function createEntity(array $data): EntityInterface
    {
        $entity = new TaskEntity();
        if (isset($data['id']) && is_int($data['id'])) {
            $entity->setId($data['id']);
        }

        if (isset($data['schedule_id']) && is_int($data['schedule_id'])) {
            $entity->setScheduleId($data['schedule_id']);
        }

        if (isset($data['start_time']) && is_int($data['start_time'])) {
            $entity->setStartTime(date_create()->setTimestamp($data['start_time']));
        }

        if ((isset($data['end_time']) && is_int($data['end_time']))
            || (isset($data['start_time']) && is_int($data['start_time'])
                && isset($data['duration']) && is_int($data['duration']))) {
            $endTime = $data['end_time'] ?? $data['start_time'] + $data['duration'];
            $entity->setEndTime(date_create()->setTimestamp($endTime));
        }

        return $entity;
    }

    /**
     * @throws InvalidCollectionDataProvidedException
     */
    public function createCollection(array $data): CollectionInterface
    {
        return (new TaskCollection())->createFromArray($data, $this);
    }
}
