<?php
namespace Volantus\MotorStatusService\Src\MotorStatus;

use Volantus\FlightBase\Src\General\Motor\MotorStatus;
use Volantus\FlightBase\Src\General\MSP\MspRepository;
use Volantus\MSPProtocol\Src\Protocol\Request\MotorStatus as MotorStatusRequest;
use Volantus\MSPProtocol\Src\Protocol\Response\MotorStatus as MotorStatusResponse;
use Volantus\MSPProtocol\Src\Protocol\Request\Request;
use Volantus\MSPProtocol\Src\Protocol\Response\Response;

/**
 * Class MotorStatusRepository
 *
 * @package Volantus\MotorStatusService\Src
 */
class MotorStatusRepository extends MspRepository
{
    /**
     * @var int
     */
    protected $priority = 3;

    /**
     * [CleanFlight ID] <=> [Volantus motor ID]
     *
     * @var array
     */
    private $idMapping = [0 => 0, 1 => 2, 2 => 4, 3 => 7, 4 => 1, 5 => 3, 6 => 5, 7 => 6];

    /**
     * [CleanFlight ID] <=> [flight controller hardware pin]
     *
     * @var array
     */
    private $pinMapping = [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8];

    /**
     * @return Request
     */
    protected function createMspRequest(): Request
    {
        return new MotorStatusRequest();
    }

    /**
     * @param MotorStatusResponse|Response $response
     *
     * @return MotorStatus
     */
    protected function decodeResponse(Response $response)
    {
        $motors = [];

        // Dummy vertical motors, until they are really implemented
        $motors[8] = ['id' => 8, 'pin' => -1, 'power' => 0.5];
        $motors[9] = ['id' => 9, 'pin' => -1, 'power' => 0.5];

        foreach ($response->getStatuses() as $i => $motorStatus) {
            if (isset($this->idMapping[$i])) {
                $id = $this->idMapping[$i];
                $motors[$id] = [
                    'id'    => $id,
                    'pin'   => $this->pinMapping[$i],
                    'power' => ($motorStatus - 1000) / 1000
                ];
            }
        }

        ksort($motors);
        return new MotorStatus($motors);
    }
}