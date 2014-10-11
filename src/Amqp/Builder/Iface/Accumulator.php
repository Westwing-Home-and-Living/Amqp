<?php
namespace Amqp\Builder\Iface;

interface Accumulator
{
    /**
     * Attaches a connection to the list of connections available already
     *
     * @param \AMQPConnection $connection The connection to be attached
     * @param string $name The name of the connection
     * @return bool
     */
    public function attachConnection(\AMQPConnection $connection, $name);

    /**
     * Attaches a channel to the list of channels available already
     *
     * @param \AMQPChannel $channel The channel to be attached
     * @param string $name The name of the channel
     *
     * @return bool
     */
    public function attachChannel(\AMQPChannel $channel, $name);

    /**
     * Attaches an exchange to the list of exchanges available already
     *
     * @param \AMQPExchange $exchange The exchange to be attached
     * @param string $name            The name of the exchange
     * @return bool
     */
    public function attachExchange(\AMQPExchange $exchange, $name);

    /**
     * Attaches a queue to the list if queues available already
     *
     * @param \AMQPQueue $queue The queue to be attached
     * @param string $name      The name of the queue
     * @return bool
     */
    public function attachQueue(\AMQPQueue $queue, $name);

    /**
     * Returns the specifies connection
     *
     * @param string $name The name of the connection requested
     * @return \AMQPConnection|bool
     */
    public function connection($name);

    /**
     * Returns a channel if it has been defined already
     *
     * @param string $name The name of the channel to be retrieved
     * @return \AMQPChannel|bool
     */
    public function channel($name);

    /**
     * Returns an exchange if it has been defined already
     *
     * @param string $name The name of the exchange to be retrieved
     * @return \AMQPExchange|bool
     */
    public function exchange($name);

    /**
     * Retrieves a queue if it has already been instantiated
     *
     * @param string $name The name of the queue to be retrieved
     * @return \AMQPQueue|bool
     */
    public function queue($name);
}