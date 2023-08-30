<?php
declare(strict_types=1);

namespace functional;

use Codeception\Example;
use Mockery as m;
use Mockery\MockInterface;
use Tymeshift\PhpTest\Components\DatabaseInterface;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleEntity;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleRepository;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleFactory;
use Tymeshift\PhpTest\Domains\Schedule\ScheduleStorage;
use Tymeshift\PhpTest\Exceptions\StorageDataMissingException;

class ScheduleCest
{
    private const SCHEDULE_ID = 1;

    private MockInterface|DatabaseInterface|null $dbMock;
    private ScheduleRepository|null $scheduleRepository;

    public function _before()
    {
        $this->dbMock = m::mock(DatabaseInterface::class);
        $this->scheduleRepository = new ScheduleRepository(
            new ScheduleStorage($this->dbMock),
            new ScheduleFactory()
        );
    }

    public function _after()
    {
        $this->dbMock = null;
        $this->scheduleRepository = null;
        m::close();
    }

    /**
     * @dataProvider scheduleProvider
     */
    public function testGetSchedule(Example $example, \UnitTester $tester): void
    {
        ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name] = $example;
        $data = ['id' => $id, 'start_time' => $startTime, 'end_time' => $endTime, 'name' => $name];

        $this->dbMock
            ->allows('query')
            ->andReturn($data);

        $entity = $this->scheduleRepository->getById($id);

        $tester->assertInstanceOf(ScheduleEntity::class, $entity);
        $tester->assertEquals($id, $entity->getId());
        $tester->assertEquals($startTime, $entity->getStartTime()->getTimestamp());
        $tester->assertEquals($endTime, $entity->getEndTime()->getTimestamp());
        $tester->assertEquals($name, $entity->getName());
    }

    public function testGetScheduleFailed(\UnitTester $tester): void
    {
        $this->dbMock
            ->allows('query')
            ->andThrow(StorageDataMissingException::class);

        $tester->expectThrowable(StorageDataMissingException::class, function () {
            $this->scheduleRepository->getById(self::SCHEDULE_ID);
        });
    }

    /**
     * @return array[]
     */
    protected function scheduleProvider(): array
    {
        return [
            ['id' => self::SCHEDULE_ID, 'start_time' => 1631232000, 'end_time' => 1631232000 + 86400, 'name' => 'Test'],
        ];
    }
}