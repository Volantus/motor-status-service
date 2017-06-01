<?php
namespace Volantus\MotorStatusService\Src\Networking;

use Ratchet\Client\WebSocket;
use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\Client\Server;
use Volantus\FlightBase\Src\General\Generic\IncomingGenericInternalMessage;
use Volantus\MSPProtocol\Src\Protocol\Response\MotorStatus as MotorStatusResponse;
use Volantus\FlightBase\Src\General\Motor\MotorStatus as MotorStatusMessage;
use Volantus\FlightBase\Src\General\MSP\MSPResponseMessage;
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


    public function test_newMessage_mspResponseHandledCorrectly()
    {
        /** @var MotorStatusResponse|\PHPUnit_Framework_MockObject_MockObject $motorStatus */
        $motorStatus = $this->getMockBuilder(MotorStatusResponse::class)->disableOriginalConstructor()->getMock();
        $mspResponse = new MSPResponseMessage('test', $motorStatus);
        $message = new IncomingGenericInternalMessage($this->server, $mspResponse);

        $this->messageService->expects(self::once())
            ->method('handle')
            ->with($this->server, 'correct')->willReturn($message);

        $this->motorStatusRepository->expects(self::once())
            ->method('onMspResponse')
            ->with(self::equalTo($this->server), self::equalTo($mspResponse))
            ->willReturn(new MotorStatusMessage([0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8]));

        $this->service->addServer($this->server);
        $this->service->newMessage($this->connection, 'correct');
    }

    public function test_newMessage_gyroStatusSentToRelayServers()
    {
        /** @var WebSocket|\PHPUnit_Framework_MockObject_MockObject $relayServerConnection */
        $relayServerConnection = $this->getMockBuilder(WebSocket::class)->disableOriginalConstructor()->getMock();
        /** @var WebSocket|\PHPUnit_Framework_MockObject_MockObject $mspServerConnection */
        $mspServerConnection = $this->getMockBuilder(WebSocket::class)->disableOriginalConstructor()->getMock();

        $mspServer = new Server($mspServerConnection, Server::ROLE_MSP_BROKER_A);
        $relayServer = new Server($relayServerConnection, Server::ROLE_RELAY_SERVER_A);

        $this->service->addServer($mspServer);
        $this->service->addServer($relayServer);

        /** @var MotorStatusResponse|\PHPUnit_Framework_MockObject_MockObject $motorStatus */
        $motorStatus = $this->getMockBuilder(MotorStatusResponse::class)->disableOriginalConstructor()->getMock();
        $mspResponse = new MSPResponseMessage('test', $motorStatus);
        $message = new IncomingGenericInternalMessage($mspServer, $mspResponse);

        $this->messageService->expects(self::once())
            ->method('handle')
            ->willReturn($message);

        $this->motorStatusRepository->method('onMspResponse') ->willReturn(new MotorStatusMessage([0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8]));

        $relayServerConnection->expects(self::once())
            ->method('send')
            ->with(self::equalTo('{"type":"motorStatus","title":"Motor status","data":{"motors":[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8]}}'));

        $mspServerConnection->expects(self::never())
            ->method('send');

        $this->service->newMessage($mspServerConnection, 'correct');
    }
}