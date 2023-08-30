<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule;

use DateTime;
use Tymeshift\PhpTest\Domains\Schedule\Item\ScheduleItemInterface;
use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\EntityInterface;

final class ScheduleEntity implements ScheduleEntityInterface
{
    private int $id;
    private string $name;
    private DateTime $startTime;
    private DateTime $endTime;

    /**
     * @var ScheduleItemInterface[]
     */
    private array $items;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ScheduleEntity
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ScheduleEntity
    {
        $this->name = $name;
        return $this;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): ScheduleEntity
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $endTime): ScheduleEntity
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return ScheduleItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(CollectionInterface $items): ScheduleEntity
    {
        $this->items = [];

        /** @var EntityInterface $item */
        foreach ($items as $item) {
            $this->items[] = $item->toScheduleItem();
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'items' => $this->items,
        ];
    }
}
