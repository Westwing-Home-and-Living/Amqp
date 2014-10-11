<?php
/**
 * Provides a way of sharing instances of objects between multiple implementations.
 * For example, the exchange builder needs a channel, which might or might not be already be instantiated.
 * If the channel was already created in the past, that the instance will be reused rather than reinitialized.
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */

namespace Amqp\Builder;

class Accumulator
{
    /**
     * Connections array
     * @var \AMQPConnection[]
     */
    protected $connections = array();

    /**
     * Channels array
     * @var \AMQPChannel[]
     */
    protected $channels = array();

    /**
     * Exchanges array
     * @var \AMQPExchange[]
     */
    protected $exchanges = array();

    /**
     * Queues array
     * @var \AMQPQueue[]
     */
    protected $queues = array();

    /**
     * Attaches a connection to the list of connections available already
     *
     * @param \AMQPConnection $connection The connection to be attached
     * @param string $name The name of the connection
     * @return bool
     */
    public function attachConnection(\AMQPConnection $connection, $name)
    {
        $this->connections[$name] = $connection;
        return true;
    }

    /**
     * Attaches a channel to the list of channels available already
     *
     * @param \AMQPChannel $channel The channel to be attached
     * @param string $name The name of the channel
     *
     * @return bool
     */
    public function attachChannel(\AMQPChannel $channel, $name)
    {
        $this->channels[$name] = $channel;
        return true;
    }

    /**
     * Attaches an exchange to the list of exchanges available already
     *
     * @param \AMQPExchange $exchange The exchange to be attached
     * @param string $name            The name of the exchange
     * @return bool
     */
    public function attachExchange(\AMQPExchange $exchange, $name)
    {
        $this->exchanges[$name] = $exchange;
        return true;
    }

    /**
     * Attaches a queue to the list if queues available already
     *
     * @param \AMQPQueue $queue The queue to be attached
     * @param string $name      The name of the queue
     * @return bool
     */
    public function attachQueue(\AMQPQueue $queue, $name)
    {
        $this->queues[$name] = $queue;
        return true;
    }

    /**
     * Returns the specifies connection
     *
     * @param string $name The name of the connection requested
     * @return \AMQPConnection|bool
     */
    public function connection($name)
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }
        return false;
    }

    /**
     * Returns a channel if it has been defined already
     *
     * @param string $name The name of the channel to be retrieved
     * @return \AMQPChannel|bool
     */
    public function channel($name)
    {
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        }
        return false;
    }

    /**
     * Returns an exchange if it has been defined already
     *
     * @param string $name The name of the exchange to be retrieved
     * @return \AMQPExchange|bool
     */
    public function exchange($name)
    {
        if (isset($this->exchanges[$name])) {
            return $this->exchanges[$name];
        }
        return false;
    }

    /**
     * Retrieves a queue if it has already been instantiated
     *
     * @param string $name The name of the queue to be retrieved
     * @return \AMQPQueue|bool
     */
    public function queue($name)
    {
        if (isset($this->queues[$name])) {
            return $this->queues[$name];
        }
        return false;
    }
}