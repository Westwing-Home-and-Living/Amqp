<?php
namespace Amqp\Configuration;

class Publisher
{
    /**
     * Channel configuration
     *
     * @var array
     */
    protected $configuration;

    const DEFAULT_TYPE = 'Buffered';

    public function setConfig(array $configuration = array())
    {
        if (!isset($configuration['type'])) {
            $configuration['type'] = self::DEFAULT_TYPE;
        } else {
            $configuration['type'] = ucwords($configuration['type']);
        }

        $this->configuration = $configuration;
    }

    public function getType()
    {
        $type =  (isset($this->configuration['type'])) ? $this->configuration['type'] : self::DEFAULT_TYPE;

        return $type;
    }
}
