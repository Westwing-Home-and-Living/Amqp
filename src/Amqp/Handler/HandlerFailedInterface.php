<?php
/**
 * Handle the failed messages resulting from the main message handler.
 *
 * Interface HandlerFailedInterface
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 *
 * @package Amqp
 */

namespace Amqp\Handler;

use Amqp\Message\Collection;

interface HandlerFailedInterface
{
    /**
     * Handles any message that issued an error/exception
     *
     * @param \Amqp\Message\Collection $collection
     *
     * @return void
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function handleFailed(Collection $collection);
}