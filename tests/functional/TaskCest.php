<?php
declare(strict_types=1);

namespace functional;

use Codeception\Example;
use Mockery as m;
use Mockery\MockInterface;
use Tymeshift\PhpTest\Components\HttpClientInterface;
use Tymeshift\PhpTest\Domains\Task\TaskCollection;
use Tymeshift\PhpTest\Domains\Task\TaskEntity;
use Tymeshift\PhpTest\Domains\Task\TaskFactory;
use Tymeshift\PhpTest\Domains\Task\TaskRepository;
use Tymeshift\PhpTest\Domains\Task\TaskStorage;
use Tymeshift\PhpTest\Exceptions\StorageDataMissingException;

class TaskCest
{
    private const SCHEDULE_ID = 1;
    private const TASK_STORAGE_BY_SCHEDULE_URL = 'https://api.enpoint.com/task/schedule_id/';

    private MockInterface|HttpClientInterface|null $httpClientMock;
    private TaskRepository|null $taskRepository;

    public function _before()
    {
        $this->httpClientMock = m::mock(HttpClientInterface::class);
        $this->taskRepository = new TaskRepository(
            new TaskStorage($this->httpClientMock),
            new TaskFactory()
        );
    }

    public function _after()
    {
        $this->httpClientMock = null;
        $this->taskRepository = null;
        m::close();
    }

    /**
     * @dataProvider tasksDataProvider
     */
    public function testGetTasks(Example $example, \UnitTester $tester): void
    {
        $data = $example->getIterator()->getArrayCopy();

        $this->httpClientMock->allows('request')
            ->with('GET', self::TASK_STORAGE_BY_SCHEDULE_URL . self::SCHEDULE_ID)
            ->andReturn($data);

        $tasks = $this->taskRepository->getByScheduleId(self::SCHEDULE_ID);

        $tester->assertInstanceOf(TaskCollection::class, $tasks);

        /** @var TaskEntity $task */
        foreach ($tasks as $key => $task) {
            $tester->assertEquals($data[$key]['id'], $task->getId());
            $tester->assertEquals($data[$key]['schedule_id'], $task->getScheduleId());
            $tester->assertEquals($data[$key]['start_time'], $task->getStartTime()->getTimestamp());
            $tester->assertEquals($data[$key]['start_time']+$data[$key]['duration'], $task->getEndTime()->getTimestamp());
        }
    }

    public function testGetTasksFailed(\UnitTester $tester): void
    {
        $this->httpClientMock
            ->allows('request')
            ->with('GET', self::TASK_STORAGE_BY_SCHEDULE_URL . self::SCHEDULE_ID)
            ->andThrow(StorageDataMissingException::class);

        $tester->expectThrowable(StorageDataMissingException::class, function () {
            $this->taskRepository->getByScheduleId(self::SCHEDULE_ID);
        });
    }

    public function tasksDataProvider(): array
    {
        return [
            [
                ["id" => 123, "schedule_id" => self::SCHEDULE_ID, "start_time" => 0, "duration" => 3600],
                ["id" => 431, "schedule_id" => self::SCHEDULE_ID, "start_time" => 3600, "duration" => 650],
                ["id" => 332, "schedule_id" => self::SCHEDULE_ID, "start_time" => 5600, "duration" => 3600],
            ]
        ];
    }
}