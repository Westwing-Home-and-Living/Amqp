<?php
namespace Amqp\Configuration;

class Channel
{
    /**
     * Channel configuration
     *
     * @var array
     */
    protected $channelConfiguration = array(
        'qos_size' => 0,
        'qos_count' => 0,
    );

    public function setConfig(array $configuration = array())
    {
        if (!isset($configuration['qos_size'])) {
            $configuration['qos_size'] = 0;
        }

        if (!isset($configuration['qos_count'])) {
            $configuration['qos_count'] = 10;
        }

        $this->channelConfiguration = $configuration;
    }

    /**
     * Return the qos_size
     *
     * @return int
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getQosSize()
    {
        if (!isset($this->channelConfiguration['qos_size'])) {
            return 0;
        }
    }

    /**
     * Return the qos_count, number of messages that the server can buffer
     *
     * @return int
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getQosCount()
    {
        if (!isset($this->channelConfiguration['qos_count'])) {
            return 0;
        }
    }
}