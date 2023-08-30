<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule\Item;

interface ScheduleItemInterface
{
    public function getScheduleId(): int;

    public function getStartTime(): int;

    public function getEndTime(): int;

    public function getType(): string;
}
