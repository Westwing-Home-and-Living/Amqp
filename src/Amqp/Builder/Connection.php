<?php
/**
 * Returns and stores if needed a connection to an AMQP broker
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Builder;

use Amqp\Builder\Iface\Accumulator as IAccumulator;

class Connection
{
    /**
     * The accumulator object
     * @var IAccumulator
     */
    protected $accumulator;

    /**
     * The configuration object
     * @var \stdClass
     */
    protected $configuration;

    public function __construct(\stdClass $configuration, IAccumulator $accumulator)
    {
        $this->configuration = $configuration;
        $this->accumulator = $accumulator;
    }

    /**
     * Returns the connection that was requested
     *
     * @param string $name The name of the connection
     * @return \AMQPConnection|bool
     * @throws Exception If the connection definition cannot be located
     */
    public function connection($name)
    {
        // does the accumulator have the connection?
        $connection = $this->accumulator->connection($name);
        if ($connection == false) {
            // need to initialize the connection
            if (!isset($this->configuration->connections[$name])) {
                throw new Exception("Connection definition cannot be located [" . $name . "]!");
            }
            $connection = new \AMQPConnection($this->configuration->connections[$name]);
            $connection->connect();
            $this->accumulator->attachConnection($connection, $name);
        }
        return $connection;
    }
}