<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule\Item;

use Tymeshift\PhpTest\Interfaces\EntityInterface;

abstract class ScheduleItemSource implements EntityInterface
{
    protected static string $entityType;

    public function toScheduleItem(): ScheduleItemInterface
    {
        $item = new ScheduleItem();
        $item->setScheduleId($this->getScheduleId());
        $item->setStartTime($this->getStartTime()->getTimestamp());
        $item->setEndTime($this->getEndTime()->getTimestamp());
        $item->setType(static::$entityType);

        return $item;
    }
}
