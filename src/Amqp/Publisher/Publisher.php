<?php
/**
 * PHP Version 5
 *
 * Implements a publisher for an AMQP enabled broker.
 * The current implementation end goal will be to deliver a AMQPExchange instance that can
 * be used in order to publish messages over the broker.
 * The object will read the configuration file provided if there is one, or receive the
 * configuration via an array. Will initialize the connection, channel, exchange for the
 * requested configuration.
 *
 * The class provides a lazy implementation. The actual connection and channels will not be
 * opened until the publish is called.
 *
 * @category AMQP
 * @package  AMQP
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */

namespace Amqp\Publisher;

use Amqp\Amqp;
use Amqp\Message\Collection;
use Amqp\Message\SingleMessageInterface;
use Amqp\Configuration\Configuration;

class Publisher extends Amqp implements PublisherInterface
{
    /**
     * Attempts to publish a message based on the given exchange configuration
     * If the connection/channels have not been opened yet, it will also attempt to
     * establish a connection to the AMQP broker
     *
     * @param SingleMessageInterface $message The message to be published on the AMQP broker
     *
     * @return boolean
     */
    public function publish(SingleMessageInterface $message)
    {
        // in the end one message is actually a collection of messages with
        // only one message inside, but still respects the semantics of a message
        $collection = new Collection();
        $collection->setRoutingKey($message->getRoutingKey())
            ->setAttributes($message->getAttributes())
            ->setFlags($message->getFlags());
        $collection->offsetSet(null, $message->getContent());

        $result = $this->push($collection);

        return $result;
    }
}
