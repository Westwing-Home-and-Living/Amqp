<?php
namespace Amqp\Builder;

use Amqp\Builder\Iface\Accumulator as IAccumulator;

class Channel
{
    /**
     * @var \stdClass
     */
    protected $configuration;

    /**
     * @var IAccumulator
     */
    protected $accumulator;

    public function __construct(\stdClass $configuration, IAccumulator $accumulator)
    {
        $this->configuration = $configuration;
        $this->accumulator = $accumulator;
    }

    /**
     * Return the channel that was requested
     *
     * @param string $name The name of the channel to be used
     * @return \AMQPChannel|bool
     * @throws Exception If the channel definition cannot be located
     */
    public function channel($name)
    {
        // check if the accumulator has the channel we want
        $channel = $this->accumulator->channel($name);
        if ($channel == false) {
            if (!isset($this->configuration->channels[$name])) {
                throw new Exception("Cannot locate definition for channel " . $channel . "!");
            }

            $channelConfig = $this->configuration->channels[$name];

            $connection = $this->connection($name);
            $channel = new \AMQPChannel($connection);
            if (isset($channelConfig['prefetchCount']) && isset($channelConfig['prefetchSize'])) {
                $channel->qos($channelConfig['prefetchSize'], $channelConfig['prefetchCount']);
            } elseif (isset($channelConfig['prefetchCount'])) {
                $channel->setPrefetchCount($channelConfig['prefetchCount']);
            } elseif (isset($channelConfig['prefetchSize'])) {
                $channel->setPrefetchSize($channelConfig['prefetchSize']);
            }

            $this->accumulator->attachChannel($channel, $name);
        }
        return $channel;
    }

    /**
     * Create or retrieve an already existing connection
     *
     * @param string $channelName The name of the channel we need to retrieve
     * @return \AMQPConnection|bool
     * @throws Exception
     */
    protected function connection($channelName)
    {
        $connections = $this->configuration->channels[$channelName]['connection'];
        foreach ($connections as $connection) {
            // since all connections begin with @ in front of them, we need to ignore the @ sign
            $connectionName = substr($connection, 1, strlen($connection)-1);
            $result = $this->accumulator->connection($connectionName);

            // return first connection available which has been initialized
            if ($result != false) {
                return $result;
            }
        }

        // get the connection builder
        $connectionChosen = $connections[array_rand($connections)];
        // ignore the @sign
        $connectionChosenName = substr($connectionChosen, 1, strlen($connectionChosen) -1);
        $builder = new Connection($this->configuration, $this->accumulator);
        $connection = $builder->connection($connectionChosenName);
        return $connection;
    }
}