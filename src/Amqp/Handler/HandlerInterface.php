<?php

/**
 * Interface providing a unified method to handle all the incoming messages
 *
 * Interface HandlerInterface
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 *
 * @package Amqp
 */

namespace Amqp\Handler;

interface HandlerInterface
{
    /**
     * Handles the current collection received via the messaging queue.
     *
     * @param \Amqp\Message\Collection $collection
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function handleMessage(\Amqp\Message\Collection $collection);
}