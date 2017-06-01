<?php
namespace Volantus\MotorStatusService\Src\Networking;

use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\Client\MspClientService;
use Volantus\FlightBase\Src\Client\Server;
use Volantus\FlightBase\Src\General\Generic\IncomingGenericInternalMessage;
use Volantus\FlightBase\Src\General\MSP\MSPResponseMessage;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Src\Server\Messaging\IncomingMessage;
use Volantus\FlightBase\Src\Server\Messaging\MessageService;
use Volantus\MotorStatusService\Src\MotorStatus\MotorStatusRepository;
use Volantus\MSPProtocol\Src\Protocol\Response\MotorStatus;

/**
 * Class MessageHandler
 *
 * @package Volantus\GyroStatusService\Src\GyroStatus
 */
class MessageHandler extends MspClientService
{
    /**
     * @var int
     */
    protected $clientRole = ClientRole::MOTOR_STATUS_SERVICE;

    /**
     * @var MotorStatusRepository
     */
    private $motorStatusRepository;

    /**
     * MessageHandler constructor.
     *
     * @param OutputInterface       $output
     * @param MessageService        $messageService
     * @param MotorStatusRepository $motorStatusRepository
     */
    public function __construct(OutputInterface $output, MessageService $messageService, MotorStatusRepository $motorStatusRepository = null)
    {
        $this->motorStatusRepository = $motorStatusRepository ?: new MotorStatusRepository();
        parent::__construct($output, $messageService, [$this->motorStatusRepository]);
    }

    /**
     * @param IncomingMessage $incomingMessage
     */
    public function handleMessage(IncomingMessage $incomingMessage)
    {
        /** @var Server $server */
        $server = $incomingMessage->getSender();

        if ($incomingMessage instanceof IncomingGenericInternalMessage && $incomingMessage->getPayload() instanceof MSPResponseMessage) {
            /** @var MSPResponseMessage $payload */
            $payload = $incomingMessage->getPayload();

            if ($payload->getMspResponse() instanceof MotorStatus) {
                $motorStatus = $this->motorStatusRepository->onMspResponse($server, $payload);
                $this->sendToRelayServers($motorStatus);
                $this->writeGreenLine('MessageHandler', 'Received MSP motor status response from server ' . $server->getRole());
            }
        }
    }

    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        parent::setLoop($loop);

        $this->loop->addPeriodicTimer(0.1, function () {
            $this->motorStatusRepository->sendRequests();
            $this->writeInfoLine('MessageHandler', 'Sent MSP request for motor status');
        });
    }
}