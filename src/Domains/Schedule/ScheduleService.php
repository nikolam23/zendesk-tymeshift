<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule;

use Tymeshift\PhpTest\Domains\Task\TaskRepository;
use Tymeshift\PhpTest\Interfaces\EntityInterface;

final class ScheduleService
{
    public function __construct(private ScheduleRepository $scheduleRepository, private TaskRepository $taskRepository)
    {
    }

    public function getScheduleWithItems(int $scheduleId): EntityInterface
    {
        $scheduleEntity = $this->scheduleRepository->getById($scheduleId);
        $tasks = $this->taskRepository->getByScheduleId($scheduleId);

        return $scheduleEntity->setItems($tasks);
    }
}
