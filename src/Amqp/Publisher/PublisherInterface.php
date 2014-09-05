<?php
namespace Amqp\Publisher;

use Amqp\Message\SingleMessageInterface;

interface PublisherInterface
{
    /**
     * Publishes a message over a chosen messaging queue. Based on the implementation
     * the message does not need to be published immediately
     *
     * @param SingleMessageInterface $message The message to be published
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function publish(SingleMessageInterface $message);
}
