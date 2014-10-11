<?php
namespace Amqp\Builder;

use Amqp\Builder\Iface\Accumulator as IAccumulator;

class Exchange
{
    /**
     * @var IAccumulator
     */
    protected $accumulator;

    /**
     * @var \stdClass
     */
    protected $config;

    public function __construct(\stdClass $configuration, IAccumulator $accumulator)
    {
        $this->accumulator = $accumulator;
        $this->config = $configuration;
    }

    public function exchange($name)
    {
        // check if the exchange has already been initialized
        $exchange = $this->accumulator->exchange($name);
        if ($exchange == false) {
            // check if the exchange definition exists
            if (!isset($this->config->exchanges[$name])) {
                throw new Exception($this->config->exchanges[$name]);
            }

            // initialize the exchange
            // get the channel builder
            $channelBuilder = new Channel($this->config, $this->accumulator);
            $channel = $channelBuilder->channel($this->config->exchanges[$name]['channel']);

            $exchange = new \AMQPExchange($channel);
            $exchange->setName($this->getName($name));
        }
    }

    protected function getName($exchangeName)
    {
        $name = $this->config->exchanges[$name]['name'];
        $nameCallable = $this->config->exchanges[$name]['dynamicName'];

        if ($nameCallable == true) {
            // get the instance and the method name
            list($class, $method) = array_values(array_filter(explode("->", $name)));
            $instance = new $class();
            $name = $instance->{$method}();
        }

        return $name;
    }
}