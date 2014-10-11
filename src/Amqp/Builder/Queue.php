<?php
/**
 * Builder that can build an exchange based on a stdClass configuration object received
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Builder;

use Amqp\Builder\Iface\Builder;

class Queue implements Builder
{
    /**
     * The configuration object
     * @var \stdClass
     */
    protected $configuration;

    /**
     * Initialize the queue builder
     *
     * @param \stdClass $configuration The configuration object
     */
    public function __construct(\stdClass $configuration)
    {
        $this->configuration = $configuration;
    }

    public function build($name)
    {
        // check if the requested queue is defined
        if (!isset($this->configuration->queues)) {
            throw new Exception("Cannot locate queue definitions in configuration!");
        }

        if (!isset($this->configuration->queues[$name])) {
            throw new Exception("Cannot locate queue definition for " . $name);
        }

        // all ok, start building the queue
        $this->buildQueue();
    }

    protected function buildQueue()
    {
        // get the dependency graphs

    }
}