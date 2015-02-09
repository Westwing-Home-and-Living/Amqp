<?php
namespace Amqp;

use Amqp\Configuration\Configuration;
use Amqp\Message\Collection;

abstract class Amqp
{
    /**
     * The configuration
     * @var Configuration
     */
    protected $configuration;

    /**
     * The AMQPExchange object currently used
     * @var \AMQPExchange
     */
    protected $exchange;

    /**
     * @param \AMQPExchange $exchange
     */
    public function __construct(\AMQPExchange $exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * Pushes a collection of messages on the AMQP broker
     *
     * @param Collection $collection
     * s
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function push(Collection $collection)
    {
        $attributes = $collection->getAttributes();
        if (!isset($attributes['timestamp'])) {
            $attributes['timestamp'] = time();
        }
        $result = $this->exchange->publish(
            (string) $collection,
            $collection->getRoutingKey(),
            $collection->getFlags(),
            $collection->getAttributes()
        );

        return $result;
    }
}
