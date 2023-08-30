<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

interface TaskStorageInterface
{
    public function getByScheduleId(int $id): array;
}
