<?php
declare(strict_types=1);

namespace unit;

use Codeception\Example;
use Mockery as m;
use Mockery\MockInterface;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleCollection;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleEntity;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleRepository;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleStorage;
use Tymeshift\PhpTest\Exceptions\InvalidCollectionDataProvidedException;
use Tymeshift\PhpTest\Exceptions\StorageDataMissingException;
use Tymeshift\PhpTest\Interfaces\FactoryInterface;
use Tymeshift\PhpTest\Interfaces\StorageInterface;

class ScheduleRepositoryCest
{
    private const SCHEDULE_ID_1 = 4;
    private const SCHEDULE_ID_2 = 9;
    private const SCHEDULE_ID_3 = 17;
    private const SCHEDULE_IDS = [
        self::SCHEDULE_ID_1,
        self::SCHEDULE_ID_2,
        self::SCHEDULE_ID_3,
    ];
    private const EMPTY_DATA = [];

    private MockInterface|ScheduleStorage|null $scheduleStorageMock;
    private MockInterface|FactoryInterface|null $scheduleFactoryMock;
    private ScheduleRepository|null $scheduleRepository;

    public function _before(): void
    {
        $this->scheduleStorageMock = m::mock(StorageInterface::class);
        $this->scheduleFactoryMock = m::mock(FactoryInterface::class);
        $this->scheduleRepository = new ScheduleRepository($this->scheduleStorageMock, $this->scheduleFactoryMock);
    }

    public function _after(): void
    {
        $this->scheduleStorageMock = null;
        $this->scheduleFactoryMock = null;
        $this->scheduleRepository = null;
        m::close();
    }

    /**
     * @dataProvider singleScheduleDataProvider
     */
    public function testGetById(Example $example, \UnitTester $tester): void
    {
        ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name] = $example;
        $data = ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name];
        $scheduleEntity = $this->createScheduleEntity($data);

        $this->scheduleStorageMock
            ->allows('getById')
            ->with($id)
            ->andReturn($data);

        $this->scheduleFactoryMock
            ->allows('createEntity')
            ->with($data)
            ->andReturn($scheduleEntity);

        $tester->assertEquals($scheduleEntity, $this->scheduleRepository->getById($id));
    }

    public function testGetByIdFailed(\UnitTester $tester): void
    {
        $this->scheduleStorageMock
            ->allows('getById')
            ->with(self::SCHEDULE_ID_1)
            ->andReturn(self::EMPTY_DATA);

        $this->scheduleFactoryMock
            ->allows('createEntity')
            ->with(self::EMPTY_DATA)
            ->andThrow(StorageDataMissingException::class);

        $tester->expectThrowable(StorageDataMissingException::class, function () {
            $this->scheduleRepository->getById(self::SCHEDULE_ID_1);
        });
    }

    /**
     * @dataProvider multiSchedulesDataProvider
     */
    public function testGetByIds(Example $example, \UnitTester $tester): void
    {
        $data = $example->getIterator()->getArrayCopy();
        $scheduleCollection = $this->createScheduleCollection($data);

        $this->scheduleStorageMock
            ->allows('getByIds')
            ->with(self::SCHEDULE_IDS)
            ->andReturn($data);

        $this->scheduleFactoryMock
            ->allows('createCollection')
            ->with($data)
            ->andReturn($scheduleCollection);

        $tester->assertEquals($scheduleCollection, $this->scheduleRepository->getByIds(self::SCHEDULE_IDS));
    }

    public function testGetByIdsFailed(\UnitTester $tester): void
    {
        $this->scheduleStorageMock
            ->allows('getByIds')
            ->with(self::SCHEDULE_IDS)
            ->andReturn(self::EMPTY_DATA);

        $this->scheduleFactoryMock
            ->allows('createCollection')
            ->with(self::EMPTY_DATA)
            ->andThrow(InvalidCollectionDataProvidedException::class);

        $tester->expectThrowable(InvalidCollectionDataProvidedException::class, function () {
            $this->scheduleRepository->getByIds(self::SCHEDULE_IDS);
        });
    }

    protected function singleScheduleDataProvider(): array
    {
        return [
            [
                'id' => self::SCHEDULE_ID_1,
                'start_time' => 1631232000,
                'end_time' => 1631232000 + 86400,
                'name' => 'Test'
            ],
        ];
    }

    protected function multiSchedulesDataProvider(): array
    {
        return [
            [
                [
                    'id' => self::SCHEDULE_ID_1,
                    'start_time' => 1631232000,
                    'end_time' => 1631232000 + 86400,
                    'name' => 'Test1'
                ],
                [
                    'id' => self::SCHEDULE_ID_2,
                    'start_time' => 1631233000,
                    'end_time' => 1631233000 + 86400,
                    'name' => 'Test2'
                ],
                [
                    'id' => self::SCHEDULE_ID_3,
                    'start_time' => 1631234000,
                    'end_time' => 1631234000 + 86400,
                    'name' => 'Test3'
                ],
            ]
        ];
    }

    private function createScheduleEntity(array $data): ScheduleEntity
    {
        $entity = new ScheduleEntity();
        $entity->setId($data['id']);
        $entity->setName($data['name']);
        $entity->setStartTime(date_create()->setTimestamp($data['start_time']));
        $entity->setEndTime(date_create()->setTimestamp($data['end_time']));

        return $entity;
    }

    private function createScheduleCollection(array $data): ScheduleCollection
    {
        $collection = new ScheduleCollection();
        foreach ($data as $item) {
            $collection->add($this->createScheduleEntity($item));
        }

        return $collection;
    }
}
