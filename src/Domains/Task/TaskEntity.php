<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

use DateTime;
use Tymeshift\PhpTest\Domains\Schedule\Item\ScheduleItemSource;
use Tymeshift\PhpTest\Interfaces\EntityInterface;

final class TaskEntity extends ScheduleItemSource implements EntityInterface
{
    protected static string $entityType = 'Task';

    private int $id;
    private int $scheduleId;
    private DateTime $startTime;
    private DateTime $endTime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TaskEntity
    {
        $this->id = $id;
        return $this;
    }

    public function getScheduleId(): int
    {
        return $this->scheduleId;
    }

    public function setScheduleId(int $scheduleId): TaskEntity
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): TaskEntity
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'scheduleId' => $this->scheduleId,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
        ];
    }
}
