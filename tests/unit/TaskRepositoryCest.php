<?php
declare(strict_types=1);

namespace unit;

use Codeception\Example;
use Mockery as m;
use Mockery\MockInterface;
use Tymeshift\PhpTest\Domains\Task\TaskCollection;
use Tymeshift\PhpTest\Domains\Task\TaskEntity;
use Tymeshift\PhpTest\Domains\Task\TaskRepository;
use Tymeshift\PhpTest\Domains\Task\TaskStorage;
use Tymeshift\PhpTest\Domains\Task\TaskStorageInterface;
use Tymeshift\PhpTest\Exceptions\InvalidCollectionDataProvidedException;
use Tymeshift\PhpTest\Exceptions\StorageDataMissingException;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;

class TaskRepositoryCest
{
    private const TASK_ID_1 = 123;
    private const TASK_ID_2 = 431;
    private const TASK_ID_3 = 332;
    private const TASK_IDS = [
        self::TASK_ID_1,
        self::TASK_ID_2,
        self::TASK_ID_3,
    ];
    private const EMPTY_DATA = [];

    private MockInterface|TaskStorage|null $taskStorageMock;
    private MockInterface|FactoryInterface|null $taskFactoryMock;
    private TaskRepository|null $taskRepository;

    public function _before()
    {
        $this->taskStorageMock = m::mock(TaskStorageInterface::class);
        $this->taskFactoryMock = m::mock(FactoryInterface::class);
        $this->taskRepository = new TaskRepository($this->taskStorageMock, $this->taskFactoryMock);
    }

    public function _after()
    {
        $this->taskStorageMock = null;
        $this->taskFactoryMock = null;
        $this->taskRepository = null;
        m::close();
    }

    /**
     * @dataProvider singleTaskDataProvider
     */
    public function testGetById(Example $example, \UnitTester $tester): void
    {
        ['id' => $id, 'schedule_id' => $startTime, 'start_time' => $endTime, 'duration' => $name] = $example;
        $data = ['id' => $id, 'schedule_id' => $startTime, 'start_time' => $endTime, 'duration' => $name];
        $taskEntity = $this->createTaskEntity($data);

        $this->taskStorageMock
            ->allows('getById')
            ->with($id)
            ->andReturn($data);

        $this->taskFactoryMock
            ->allows('createEntity')
            ->with($data)
            ->andReturn($taskEntity);

        $tester->assertEquals($taskEntity, $this->taskRepository->getById($id));
    }

    public function testGetByIdFailed(\UnitTester $tester): void
    {
        $this->taskStorageMock
            ->allows('getById')
            ->with(self::TASK_ID_1)
            ->andReturn(self::EMPTY_DATA);

        $this->taskFactoryMock
            ->allows('createEntity')
            ->with(self::EMPTY_DATA)
            ->andThrow(StorageDataMissingException::class);

        $tester->expectThrowable(StorageDataMissingException::class, function () {
            $this->taskRepository->getById(self::TASK_ID_1);
        });
    }

    /**
     * @dataProvider multiTasksDataProvider
     */
    public function testGetByScheduleId(Example $example, \UnitTester $tester): void
    {
        $data = $example->getIterator()->getArrayCopy();
        $taskCollection = $this->createTaskCollection($data);

        $this->taskStorageMock
            ->allows('getByScheduleId')
            ->with(self::TASK_ID_1)
            ->andReturns($data);

        $this->taskFactoryMock
            ->allows('createCollection')
            ->with($data)
            ->andReturn($taskCollection);

        $tester->assertEquals($taskCollection, $this->taskRepository->getByScheduleId(self::TASK_ID_1));
    }

    public function testGetByScheduleIdFailed(\UnitTester $tester): void
    {
        $this->taskStorageMock
            ->allows('getByScheduleId')
            ->with(self::TASK_ID_1)
            ->andReturn([]);

        $this->taskFactoryMock
            ->allows('createCollection')
            ->with([])
            ->andThrow(InvalidCollectionDataProvidedException::class);

        $tester->expectThrowable(InvalidCollectionDataProvidedException::class, function () {
            $this->taskRepository->getByScheduleId(self::TASK_ID_1);
        });
    }

    /**
     * @dataProvider multiTasksDataProvider
     */
    public function testGetByIds(Example $example, \UnitTester $tester): void
    {
        $data = $example->getIterator()->getArrayCopy();
        $taskCollection = $this->createTaskCollection($data);

        $this->taskStorageMock
            ->allows('getByIds')
            ->with(self::TASK_IDS)
            ->andReturn($data);

        $this->taskFactoryMock
            ->allows('createCollection')
            ->with($data)
            ->andReturn($taskCollection);

        $tester->assertEquals($taskCollection, $this->taskRepository->getByIds(self::TASK_IDS));
    }

    public function testGetByIdsFailed(\UnitTester $tester): void
    {
        $this->taskStorageMock
            ->allows('getByIds')
            ->with(self::TASK_IDS)
            ->andReturn(self::EMPTY_DATA);

        $this->taskFactoryMock
            ->allows('createCollection')
            ->with(self::EMPTY_DATA)
            ->andThrow(InvalidCollectionDataProvidedException::class);

        $tester->expectThrowable(InvalidCollectionDataProvidedException::class, function () {
            $this->taskRepository->getByIds(self::TASK_IDS);
        });
    }

    public function singleTaskDataProvider(): array
    {
        return [
            ["id" => self::TASK_ID_1, "schedule_id" => 1, "start_time" => 0, "duration" => 3600],
        ];
    }

    public function multiTasksDataProvider(): array
    {
        return [
            [
                ["id" => self::TASK_ID_1, "schedule_id" => 1, "start_time" => 0, "duration" => 3600],
                ["id" => self::TASK_ID_2, "schedule_id" => 1, "start_time" => 3600, "duration" => 650],
                ["id" => self::TASK_ID_3, "schedule_id" => 1, "start_time" => 5600, "duration" => 3600],
            ]
        ];
    }

    private function createTaskEntity(array $data): TaskEntity
    {
        $entity = new TaskEntity();
        $entity->setId($data['id']);
        $entity->setScheduleId($data['schedule_id']);
        $entity->setStartTime(date_create()->setTimestamp($data['start_time']));
        $entity->setEndTime(date_create()->setTimestamp($data['start_time'] + $data['duration']));

        return $entity;
    }

    private function createTaskCollection(array $data): TaskCollection
    {
        $collection = new TaskCollection();
        foreach ($data as $item) {
            $collection->add($this->createTaskEntity($item));
        }

        return $collection;
    }
}
