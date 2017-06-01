<?php
namespace Volantus\MotorStatusService\Src\Networking;

use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\Client\MspClientService;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Src\Server\Messaging\IncomingMessage;
use Volantus\FlightBase\Src\Server\Messaging\MessageService;
use Volantus\MotorStatusService\Src\MotorStatus\MotorStatusRepository;

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
    }
}