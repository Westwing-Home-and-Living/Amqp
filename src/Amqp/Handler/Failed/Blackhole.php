<?php
/**
 * Blackhole handler for failed messages
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 *
 * @package Amqp
 */
namespace Amqp\Handler\Failed;

use Amqp\Handler\HandlerFailedInterface;
use Amqp\Message\Collection;

class Blackhole implements HandlerFailedInterface
{

    /**
     * Discards all the failed messages coming from the messaging queue.
     * To be used for tests only
     *
     * @param Collection $collection
     *
     * @return void
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function handleFailed(Collection $collection)
    {}
}