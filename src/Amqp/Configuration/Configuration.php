<?php
namespace Amqp\Configuration;

use Amqp\Exception\ConfigurationException as ConfigException;

class Configuration
{
    /**
     * The connection configuration
     *
     * @var Connection
     */
    protected $connectionConfiguration;

    /**
     * Channel configuration
     *
     * @var Channel
     */
    protected $channelConfiguration;

    /**
     * The exchange configuration
     *
     * @var Exchange
     */
    protected $exchangeConfiguration;

    /**
     * The queue configuration
     *
     * @var Queue
     */
    protected $queueConfiguration;

    /**
     * Initialize the main config object. The required values are restricted to
     * connection and exchange
     *
     * @param array $configuration The main configuration array
     *
     * @throws ConfigException If the exchange configuration is not present
     */
    public function __construct(array $configuration)
    {
        if (!isset($configuration['exchange'])) {
            throw new ConfigException(
                "Missing required exchange configuration"
            );
        }

        // initialize the configurations
        if (isset($configuration['connection'])) {
            $configConn = $configuration['connection'];
        } else {
            $configConn = array();
        }
        $this->initConnectionConfig($configConn);

        if (isset($configuration['channel'])) {
            $configChan = $configuration['channel'];
        } else {
            $configChan = array();
        }
        $this->initChannelConfig($configChan);

        $this->initExchangeConfig($configuration['exchange']);

        if (isset($configuration['queue'])) {
            $configQue = $configuration['queue'];
        } else {
            $configQue = array();
        }
        $this->initQueueConfiguration($configQue);
    }

    /**
     * Initialize the connection configuration. Since most values can be reduced
     * to defaults, we do not need it to be necessarily present in the configuration
     *
     * @param array $config
     *
     * @return Connection
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initConnectionConfig(array $config = array())
    {
        $connectionConfig = new Connection($config);

        $this->connectionConfiguration = $connectionConfig;

        return $connectionConfig;
    }

    /**
     * Initialize the channel configuration. Since all values have defaults, we
     * don't necessarily need to have this configuration in the main configuration
     * array
     *
     * @param array $config
     *
     * @return Channel
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initChannelConfig(array $config = array())
    {

        $channelConfig = new Channel();
        $channelConfig->setConfig($config);

        $this->channelConfiguration = $channelConfig;

        return $channelConfig;
    }

    /**
     * Initialize the exchange configuration. Since we need the name of the exchange,
     * this needs to be present in the configuration array
     *
     * @param array $config
     *
     * @return Exchange
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initExchangeConfig($config)
    {
        $exchangeConfig = new Exchange($config);

        $this->exchangeConfiguration = $exchangeConfig;

        return $exchangeConfig;
    }

    /**
     * Initialize the queue configuration. Since all the queue parameters have
     * a default value, than we don't necessarily need to receive the config
     *
     * @param array $config
     * @return Queue
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initQueueConfiguration(array $config = array())
    {
        $queueConfig = new Queue();
        $queueConfig->setConfig($config);

        $this->queueConfiguration = $queueConfig;

        return $queueConfig;
    }

    /**
     * Returns the connection configuration
     *
     * @return Connection
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getConnectionConfiguration()
    {
        return $this->connectionConfiguration;
    }

    /**
     * Allows overriding the connection configuration details
     *
     * @param array $config
     *
     * @return Connection
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setConnectionConfiguration(array $config)
    {
        return $this->initConnectionConfig($config);
    }

    /**
     * Returns the channel configuration
     *
     * @return Channel
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getChannelConfiguration()
    {
        return $this->channelConfiguration;
    }

    /**
     * Allows overriding the channel configuration
     *
     * @param array $config
     *
     * @return Channel
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setChannelConfiguration(array $config)
    {
        return $this->initChannelConfig($config);
    }

    /**
     * Returns the exchange configuration
     *
     * @return Exchange
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getExchangeConfiguration()
    {
        return $this->exchangeConfiguration;
    }

    /**
     * Sets the exchange configuration
     *
     * @param array $config
     *
     * @return Exchange
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setExchangeConfiguration(array $config)
    {
        return $this->initExchangeConfig($config);
    }

    /**
     * Returns the queue configuration
     *
     * @return Queue
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getQueueConfiguration()
    {
        return $this->queueConfiguration;
    }

    /**
     * The queue configuration public accesor
     *
     * @param array $config
     *
     * @return Queue
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setQueueConfiguration(array $config)
    {
        return $this->initQueueConfiguration($config);
    }
}
