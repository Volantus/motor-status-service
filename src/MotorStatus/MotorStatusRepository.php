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
     * [CleanFlight PIN/ID] <=> [flight controller hardware pin]
     *
     * @var array
     */
    private $pinMapping = [
        5 => 0,
        2 => 1,
        6 => 2,
        3 => 3,
        7 => 4,
        4 => 5,
        8 => 6,
        1 => 7
    ];

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

        foreach ($response->getStatuses() as $pinId => $motorStatus) {
            $pinId++;
            if (isset($this->pinMapping[$pinId])) {
                $id = $this->pinMapping[$pinId];
                $power = ($motorStatus - 1000) / 1000;
                $motors[$id] = [
                    'id'    => $id,
                    'pin'   => $pinId,
                    'power' => $power > 0 ? $power : 0
                ];
            }
        }

        ksort($motors);
        return new MotorStatus($motors);
    }
}