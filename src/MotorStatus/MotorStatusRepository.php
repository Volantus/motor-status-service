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
        $motors = $response->getStatuses();
        foreach ($motors as &$motorStatus) {
            $motorStatus = ($motorStatus - 1000) / 1000;
        }

        return new MotorStatus($motors);
    }
}