<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Schedule;

use Tymeshift\PhpTest\Interfaces\CollectionInterface;
use Tymeshift\PhpTest\Interfaces\EntityInterface;

interface ScheduleEntityInterface extends EntityInterface
{
    public function getItems(): array;

    public function setItems(CollectionInterface $items): ScheduleEntityInterface;
}
