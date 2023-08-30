<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Domains\Task;

use Tymeshift\PhpTest\Components\HttpClientInterface;
use Tymeshift\PhpTest\Interfaces\StorageInterface;

/**
 * Implementation of each method is just for representation purposes.
 * Typically, we would check response code, handle exceptions,
 * do validation if needed, get the response content (data) etc.
 */
final class TaskStorage implements TaskStorageInterface, StorageInterface
{
    /**
     * In a practical scenario,
     * the base URL would typically be stored in the .env file
     * and then accessed through the configuration system.
     */
    private const TASK_STORAGE_URL = 'https://api.enpoint.com/task';

    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getById(int $id): array
    {
        return $this->client->request('GET', self::TASK_STORAGE_URL . '/id/' . $id);
    }

    public function getByScheduleId(int $id): array
    {
        return $this->client->request('GET', self::TASK_STORAGE_URL . '/schedule_id/' . $id);
    }

    public function getByIds(array $ids): array
    {
        return $this->client->request('GET', self::TASK_STORAGE_URL . '/ids/' . json_encode($ids));
    }
}
