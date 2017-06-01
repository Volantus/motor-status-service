<?php
namespace Volantus\MotorStatusService\Tests\MotorStatus;

use Volantus\FlightBase\Src\General\Motor\MotorStatus as MotorStatusMessage;
use Volantus\FlightBase\Src\General\MSP\MspRepository;
use Volantus\FlightBase\Tests\General\MSP\MspRepositoryTest;
use Volantus\MotorStatusService\Src\MotorStatus\MotorStatusRepository;
use Volantus\MSPProtocol\Src\Protocol\Request\MotorStatus;
use Volantus\MSPProtocol\Src\Protocol\Request\Request;
use Volantus\MSPProtocol\Src\Protocol\Response\MotorStatus as MotorStatusResponse;
use Volantus\MSPProtocol\Src\Protocol\Response\Response;

/**
 * Class MotorStatusRepositoryTest
 *
 * @package Volantus\MotorStatusService\Tests\MotorStatus
 */
class MotorStatusRepositoryTest extends MspRepositoryTest
{
    /**
     * @return MspRepository
     */
    protected function createRepository(): MspRepository
    {
        return new MotorStatusRepository([$this->serverA, $this->serverB]);
    }

    /**
     * @return int
     */
    protected function getExpectedPriority(): int
    {
        return 3;
    }

    /**
     * @return Request
     */
    protected function getExpectedMspRequest(): Request
    {
        return new MotorStatus();
    }

    /**
     * @return Response|MotorStatusResponse
     */
    protected function getCorrectMspResponse(): Response
    {
        $motors = [0, 1100, 1200, 1300, 1450, 1500, 1600, 2000];
        /** @var MotorStatusResponse|\PHPUnit_Framework_MockObject_MockObject $response */
        $response = $this->getMockBuilder(MotorStatusResponse::class)->disableOriginalConstructor()->getMock();
        $response->method('getStatuses')->willReturn($motors);

        return $response;
    }

    /**
     * @return MotorStatusMessage
     */
    protected function getExpectedDecodedResult()
    {
        return new MotorStatusMessage([
            ['id' => 0, 'pin' => 1, 'power' => 0],
            ['id' => 1, 'pin' => 5, 'power' => 0.45],
            ['id' => 2, 'pin' => 2, 'power' => 0.1],
            ['id' => 3, 'pin' => 6, 'power' => 0.5],
            ['id' => 4, 'pin' => 3, 'power' => 0.2],
            ['id' => 5, 'pin' => 7, 'power' => 0.6],
            ['id' => 6, 'pin' => 8, 'power' => 1],
            ['id' => 7, 'pin' => 4, 'power' => 0.3],
            ['id' => 8, 'pin' => -1, 'power' => 0.5],
            ['id' => 9, 'pin' => -1, 'power' => 0.5],
        ]);
    }
}