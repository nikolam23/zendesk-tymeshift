<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

use Tymeshift\PhpTest\Interfaces\CollectionInterface;

interface TaskRepositoryInterface
{
    public function getByScheduleId(int $scheduleId): CollectionInterface;
}
