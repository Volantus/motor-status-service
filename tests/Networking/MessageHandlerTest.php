<?php
namespace Volantus\MotorStatusService\Src\Networking;

use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Tests\Client\MspClientServiceTest;
use Volantus\MotorStatusService\Src\MotorStatus\MotorStatusRepository;

/**
 * Class MessageHandlerTest
 *
 * @package Volantus\MotorStatusService\Src\Networking
 */
class MessageHandlerTest extends MspClientServiceTest
{
    /**
     * @var MotorStatusRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $motorStatusRepository;

    protected function setUp()
    {
        $this->motorStatusRepository = $this->getMockBuilder(MotorStatusRepository::class)->disableOriginalConstructor()->getMock();
        $this->mspRepositories[] = $this->motorStatusRepository;
        parent::setUp();
    }

    /**
     * @return ClientService
     */
    protected function createService(): ClientService
    {
        return new MessageHandler($this->dummyOutput, $this->messageService, $this->motorStatusRepository);
    }

    /**
     * @return int
     */
    protected function getExpectedClientRole(): int
    {
        return ClientRole::MOTOR_STATUS_SERVICE;
    }
}