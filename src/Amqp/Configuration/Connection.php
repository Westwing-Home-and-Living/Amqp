<?php
namespace Amqp\Configuration;

class Connection
{
    /**
     * The connection configuration
     *
     * @var array
     */
    protected $connectionConfig = array();

    public function __construct(array $configuration)
    {
        // set the default values
        if (!isset($configuration['host'])) {
            $configuration['host'] = "localhost";
        }

        if (!isset($configuration['port'])) {
            $configuration['port'] = 5672;
        }

        if (!isset($configuration['vhost'])) {
            $configuration['vhost'] = "/";
        }

        if (!isset($configuration['login'])) {
            $configuration['login'] = 'guest';
        }

        if (!isset($configuration['password'])) {
            $configuration['password'] = 'guest';
        }

        if (!isset($configuration['read_timeout'])) {
            $configuration['read_timeout'] = 0;
        }

        $this->connectionConfig = $configuration;
    }

    public function toArray()
    {
        return $this->connectionConfig;
    }
}