<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule\Item;

final class ScheduleItem implements ScheduleItemInterface
{
    private int $scheduleId;
    private int $startTime;
    private int $endTime;
    private string $type;

    public function getScheduleId(): int
    {
        return $this->scheduleId;
    }

    public function setScheduleId(int $scheduleId): void
    {
        $this->scheduleId = $scheduleId;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function setEndTime(int $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
