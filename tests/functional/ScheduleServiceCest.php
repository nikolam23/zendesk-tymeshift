<?php
declare(strict_types=1);

namespace functional;

use Codeception\Example;
use Mockery as m;
use Mockery\MockInterface;
use Tymeshift\PhpTest\Components\DatabaseInterface;
use Tymeshift\PhpTest\Components\HttpClientInterface;
use Tymeshift\PhpTest\Domains\Schedule\Item\ScheduleItem;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleEntity;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleFactory;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleRepository;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleService;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleStorage;
use Tymeshift\PhpTest\Domains\Task\TaskFactory;
use Tymeshift\PhpTest\Domains\Task\TaskRepository;
use Tymeshift\PhpTest\Domains\Task\TaskStorage;
use Tymeshift\PhpTest\Exceptions\StorageDataMissingException;

class ScheduleServiceCest
{
    private const SCHEDULE_ID = 1;
    private const TASK_STORAGE_BY_SCHEDULE_URL = 'https://api.enpoint.com/task/schedule_id/';

    private MockInterface|DatabaseInterface|null $dbMock;
    private MockInterface|HttpClientInterface|null $httpClientMock;
    private ScheduleService|null $scheduleService;

    public function _before()
    {
        $this->dbMock = m::mock(DatabaseInterface::class);
        $this->httpClientMock = m::mock(HttpClientInterface::class);

        $scheduleRepository = new ScheduleRepository(
            new ScheduleStorage($this->dbMock),
            new ScheduleFactory()
        );

        $taskRepository = new TaskRepository(
            new TaskStorage($this->httpClientMock),
            new TaskFactory(),
        );

        $this->scheduleService = new ScheduleService(
            $scheduleRepository,
            $taskRepository,
        );
    }

    public function _after()
    {
        $this->dbMock = null;
        $this->httpClientMock = null;
        $this->scheduleService = null;
        m::close();
    }

    /**
     * @dataProvider scheduleProvider
     */
    public function testGetScheduleWithItems(Example $example, \UnitTester $tester): void
    {
        ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name, 'items' => $items] = $example;
        $data = ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name];

        $this->dbMock
            ->allows('query')
            ->andReturn($data);

        $this->httpClientMock->allows('request')
            ->with('GET', self::TASK_STORAGE_BY_SCHEDULE_URL . self::SCHEDULE_ID)
            ->andReturn($items);

        $entity = $this->scheduleService->getScheduleWithItems($id);

        $tester->assertInstanceOf(ScheduleEntity::class, $entity);
        $tester->assertEquals($id, $entity->getId());
        $tester->assertEquals($startTime, $entity->getStartTime()->getTimestamp());
        $tester->assertEquals($endTime, $entity->getEndTime()->getTimestamp());
        $tester->assertEquals($name, $entity->getName());
        $tester->assertEquals($this->getExpectedItems($items), $entity->getItems());
    }

    public function testGetScheduleWithItemsFailed(\UnitTester $tester): void
    {
        $this->dbMock
            ->allows('query')
            ->andThrow(StorageDataMissingException::class);

        $this->httpClientMock->allows('request')
            ->with('GET', self::TASK_STORAGE_BY_SCHEDULE_URL . self::SCHEDULE_ID)
            ->andThrow(StorageDataMissingException::class);

        $tester->expectThrowable(StorageDataMissingException::class, function () {
            $this->scheduleService->getScheduleWithItems(self::SCHEDULE_ID);
        });
    }

    protected function scheduleProvider(): array
    {
        $items = [
            ["id" => 123, "schedule_id" => self::SCHEDULE_ID, "start_time" => 0, "duration" => 3600],
            ["id" => 431, "schedule_id" => self::SCHEDULE_ID, "start_time" => 3600, "duration" => 650],
            ["id" => 332, "schedule_id" => self::SCHEDULE_ID, "start_time" => 5600, "duration" => 3600],
        ];

        return [
            [
                'id' => self::SCHEDULE_ID,
                'start_time' => 1631232000,
                'end_time' => 1631232000 + 86400,
                'name' => 'Test',
                'items' => $items,
            ],
        ];
    }

    private function getExpectedItems(array $items): array
    {
        foreach ($items as $key => $item) {
            $scheduleItem = new ScheduleItem();
            $scheduleItem->setScheduleId($item['schedule_id']);
            $scheduleItem->setStartTime($item['start_time']);
            $scheduleItem->setEndTime($item['start_time'] + $item['duration']);
            $scheduleItem->setType('Task');

            $items[$key] = $scheduleItem;
        }

        return $items;
    }
}