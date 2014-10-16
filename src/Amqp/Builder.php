<?php
namespace Amqp;

use Amqp\Exception\BuilderException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Amqp\Configuration\Connection;
use Amqp\Configuration\Channel;
use Amqp\Configuration\Exchange;
use Amqp\Configuration\Queue;
use Amqp\Configuration\Publisher as PublisherConfiguration;
use Amqp\Exception\PublisherException;
use Amqp\Listener\Listener;

/**
 * Class Builder
 *
 * @author Josemi Liébana <josemi.liebana@westwing.de>
 *
 * @package Amqp
 */
class Builder
{

    const CONFIGURATION_NS = "Amqp\\Configuration\\";

    const PUBLISHER_NS = "Amqp\\Publisher\\";

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * Path to the configuration file
     *
     * @var string
     */
    protected $configFile;

    /**
     * Resulting array of running the pathinfo() function on the $configFile path.
     *
     * The indexes are available through the constants:
     * <code>
     * PATHINFO_DIRNAME
     * PATHINFO_FILENAME
     * PATHINFO_EXTENSION
     * PATHINFO_BASENAME
     * </code>
     *
     * @var array
     */
    protected $pathInfo;

    /**
     * Holds messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * The publisher
     *
     * @var Publisher
     */
    protected $publisher;

    /**
     * The listener
     *
     * @var Listener
     */
    protected $listener;

    /**
     * Builds the dependency injection container based on the $configFile
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param string $configFile Full path to the configuration file
     *
     * @throws FileLoaderLoadException
     */
    public function __construct($configFile)
    {
        if (!$this->isValidConfigFile($configFile)) {
            throw new FileLoaderLoadException($this->getLastMessage());
        }

        $this->configFile = $configFile;
        $this->pathInfo   = $this->generatePathInfo($configFile);

        $this->container   = new ContainerBuilder();
        $this->fileLocator = new FileLocator(array($this->pathInfo[PATHINFO_DIRNAME]));

        $this->buildContainer($this->pathInfo);
    }

    /**
     * Return the messages
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Return the last message or null if there is no message
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @return string|null
     */
    public function getLastMessage()
    {
        if (empty($this->messages)) {
            return null;
        }

        return array_pop($this->messages);
    }

    /**
     * Builds and return an instance of the publisher based on the type parameter
     * of the configuration file.
     * Default is Buffered
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param bool $forceNew
     *
     * @throws Exception\PublisherException*
     *
     * @return \Amqp\Publisher\Buffered|\Amqp\Publisher\Publisher
     */
    public function getPublisher($forceNew = false)
    {
        if ($this->publisher instanceof Amqp && $forceNew === false) {
            return $this->publisher;
        }

        $connection = $this->initConnection();
        $channel = $this->initChannel($connection);
        $exchange = $this->initExchange($channel);

        /** @var PublisherConfiguration $publisherConfig */
        $publisherConfig    = $this->getConfig('Publisher');
        $publisherClassName = $publisherConfig->getType();

        if ($publisherClassName == "Simple") {
            $publisherQualifiedClassName = self::PUBLISHER_NS . "Publisher";
        } else {
            $publisherQualifiedClassName = self::PUBLISHER_NS . $publisherClassName;
        }

        if (!class_exists($publisherQualifiedClassName)) {
            throw new PublisherException(
                sprintf('The type %s is not a valid publisher type', $publisherQualifiedClassName)
            );
        }

        $publisher = new $publisherQualifiedClassName($exchange);

        $this->publisher = $publisher;

        return $publisher;
    }

    /**
     * Builds and return an instance of the listener
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param bool $forceNew
     *
     * @return Listener
     */
    public function getListener($forceNew = false)
    {
        if ($this->listener instanceof Listener && $forceNew === false) {
            return $this->listener;
        }

        $connection = $this->initConnection();
        $channel = $this->initChannel($connection);

        // we do not need the value of this return. Just make sure that the exchange is already there
        $this->initExchange($channel);

        $queue = $this->initQueue($channel);

        $listener = new Listener($queue);
        $this->listener = $listener;

        return $listener;
    }

    /**
     * Simple validation of the configuration file
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param string $configFile Full path to the configuration file
     *
     * @return bool
     */
    protected function isValidConfigFile($configFile)
    {
        if (empty($configFile)) {
            $this->setMessage('No file was given');
            return false;
        }

        if (!file_exists($configFile)) {
            $this->setMessage(sprintf('%s path not found', $configFile));
            return false;
        }

        return true;
    }

    /**
     * Adds a message to the messages property
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param string $message Message to be added to the messages property
     */
    protected function setMessage($message)
    {
        array_push($this->messages, $message);
    }

    /**
     * Returns a certain configuration value if it exists
     *
     * @param $name
     *
     * @return object
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function getConfig($name)
    {
        $config = $this->container->get(self::CONFIGURATION_NS . ucfirst($name));
        return $config;
    }

    /**
     * Generate an array with the pathinfo()
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param $configFile
     *
     * @return array
     */
    protected function generatePathInfo($configFile)
    {
        $pathInfoKeys = array(
            PATHINFO_DIRNAME,
            PATHINFO_FILENAME,
            PATHINFO_EXTENSION,
            PATHINFO_BASENAME,
        );
        $pathInfo = array_combine($pathInfoKeys, pathinfo($configFile));

        return $pathInfo;
    }

    /**
     * Parse the configuration and build the container
     *
     * @author Josemi Liébana <josemi.liebana@westwing.de>
     *
     * @param array $pathInfo
     *
     * @throws FileLoaderLoadException
     */
    protected function buildContainer(array $pathInfo)
    {
        $extension = $pathInfo[PATHINFO_EXTENSION];

        switch($extension) {
            case 'xml':
                $containerLoader = new XmlFileLoader($this->container, $this->fileLocator);
                break;
            case 'ini':
                $containerLoader = new IniFileLoader($this->container, $this->fileLocator);
                break;
            case 'yml':
                $containerLoader = new YamlFileLoader($this->container, $this->fileLocator);
                break;
            case 'php':
                $containerLoader = new PhpFileLoader($this->container, $this->fileLocator);
                break;
            default:
                throw new FileLoaderLoadException($extension);
                break;
        }

        $containerLoader->load($pathInfo[PATHINFO_FILENAME]);
    }

    /**
     * Prepares a connection to the AMQP broker
     *
     * @return \AMQPConnection
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initConnection()
    {
        /** @var array $config */
        $config = $this->getConfig('Connection')->toArray();

        $connection = new \AMQPConnection($config);
        $connection->connect();

        return $connection;
    }

    /**
     * Initialize the channel
     *
     * @param \AMQPConnection $connection
     *
     * @return \AMQPChannel
     *
     * @throws BuilderException If the qos setting failed for some reason
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initChannel(\AMQPConnection $connection)
    {
        /** @var \Amqp\Configuration\Channel $config */
        $config = $this->getConfig('Channel');
        $channel = new \AMQPChannel($connection);

        $result = $channel->qos($config->getQosSize(), $config->getQosCount());
        if ($result === false) {
            throw new BuilderException("Qos configuration error");
        }

        return $channel;
    }

    /**
     * Initializes the exchange according to the specifications
     *
     * @param \AMQPChannel $channel
     *
     * @return \AMQPExchange
     *
     * @throws BuilderException If the name cannot be set
     * @throws BuilderException If the type cannot be set
     * @throws BuilderException If the flags cannot be set
     * @throws BuilderException If the arguments cannot be set
     * @throws BuilderException If the exchange cannot be declared
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initExchange(\AMQPChannel $channel)
    {
        /** @var \Amqp\Configuration\Exchange $config */
        $config = $this->getConfig('Exchange');

        $exchange = new \AMQPExchange($channel);
        $result = $exchange->setName($config->getName());
        if ($result === false) {
            throw new BuilderException("Exchange name configuration error");
        }

        $result = $exchange->setType($config->getType());
        if ($result === false) {
            throw new BuilderException("Exchange type configuration error");
        }

        $result = $exchange->setFlags($config->getFlags());
        if ($result === false) {
            throw new BuilderException("Exchange flags configuration error");
        }

        $result = $exchange->setArguments($config->getArguments());
        if ($result === false) {
            throw new BuilderException("Exchange arguments configuration error");
        }

        $result = $exchange->declareExchange();
        if ($result === false) {
            throw new BuilderException("Exchange cannot be declared");
        }

        return $exchange;
    }

    /**
     * Initialize the queue
     *
     * @param \AMQPChannel $channel
     *
     * @return \AMQPQueue
     *
     * @throws Exception\BuilderException If the name cannot be set
     * @throws Exception\BuilderException If the arguments cannot be set
     * @throws Exception\BuilderException If the flags cannot be set
     * @throws Exception\BuilderException If the queue cannot be declared
     * @throws Exception\BuilderException If the queue cannot be binded to the exchange
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function initQueue(\AMQPChannel $channel)
    {
        /** @var \Amqp\Configuration\Queue $config */
        $config = $this->getConfig('Queue');

        /** @var \Amqp\Configuration\Exchange $exchangeConfig */
        $exchangeConfig = $this->getConfig('Exchange');

        $queue = new \AMQPQueue($channel);
        $name = $config->getName();
        if (!empty($name)) {
            $result = $queue->setName($config->getName());
            if ($result === false) {
                throw new BuilderException("Queue name configuration error");
            }
        }

        $result = $queue->setArguments($config->getArguments());
        if ($result === false) {
            throw new BuilderException("Queue arguments configuration error");
        }

        $result = $queue->setFlags($config->getFlags());
        if ($result === false) {
            throw new BuilderException("Queue flags configuration error");
        }

        $result = $queue->declareQueue();
        if ($result === false) {
            throw new BuilderException("Queue cannot be declared");
        }

        $result = $queue->bind($exchangeConfig->getName(), $config->getRoutingKey(), $config->getBindingArguments());
        if ($result === false) {
            throw new BuilderException("Queue cannot be binded to exchange");
        }

        return $queue;
    }
}
