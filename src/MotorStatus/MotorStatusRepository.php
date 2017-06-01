<?php
namespace Volantus\MotorStatusService\Src\MotorStatus;

use Volantus\FlightBase\Src\General\MSP\MspRepository;
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
        // TODO: Implement createMspRequest() method.
    }

    /**
     * @param Response $response
     *
     * @return mixed
     */
    protected function decodeResponse(Response $response)
    {
        // TODO: Implement decodeResponse() method.
    }
}