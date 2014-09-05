<?php
/**
 * Class representing the basic message that can be sent over to an AMQP broker
 * Provides the hashing functionality for working with messages and also for
 * being able to bundle messages based on their properties to be able to buffer
 * them into packs.
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Message;

use Amqp\Exception\MessageException;

class Message implements SingleMessageInterface
{
    /**
     * The content of the message. Always a string.
     * @var mixed
     */
    protected $content;

    /**
     * MessageException flags. One or more of AMQP_MANDATORY, AMQP_IMMEDIATE
     * @var int
     */
    protected $flags = AMQP_NOPARAM;

    /**
     * @see \AMQPExchange::publish() attributes parameter for a list of
     *                               possible message attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * The routing key for the current message
     *
     * @var string
     */
    protected $routingKey = null;

    /**
     * Initialize the message object
     * Object contents are not allowed.
     *
     * @param mixed $content
     *
     * @todo implement message serialization for object content?
     */
    public function __construct($content)
    {
        $this->setContent($content);
    }

    /**
     * Sets the message type
     *
     * @param $type
     *
     * @return $this
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setType($type)
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    /**
     * Get the message type
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getType()
    {
        if (!isset($this->attributes['type'])) {
            return false;
        }

        return $this->attributes['type'];
    }

    /**
     * Sets the content of the message
     *
     * @param mixed $content
     *
     * @return $this
     *
     * @throws MessageException If the body of the message is an object
     */
    public function setContent($content)
    {
        // objects are not allowed
        if (is_object($content)) {
            throw new MessageException('MessageException body cannot be object');
        }

        $this->content = $content;

        return $this;
    }

    /**
     * Sets the message flags
     *
     * @param int $flags The associated message flags
     *
     * @return $this
     */
    public function setFlags($flags = AMQP_NOPARAM)
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * The arguments for the message
     *
     * @param array $attributes The message attributes in the form of array
     *
     * @return $this
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Sets the routing key for the current message
     *
     * @param string $routingKey
     *
     * @return $this
     *
     * @throws MessageException If the routing key is not a string or null
     */
    public function setRoutingKey($routingKey = null)
    {
        if (!is_string($routingKey) || is_null($routingKey)) {
            throw new MessageException(
                "The routing key needs to be either a string or null"
            );
        }
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * Returns the content of a message
     *
     * @return mixed
     */
    public function getContent()
    {
        $content = $this->content;

        return $content;
    }

    /**
     * Returns the routing key for the current message
     *
     * @return mixed can return either a null or a string routing key
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * Returns the current message flags, a bitmask of one or more possible flags.
     *
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Returns the current message attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns the message buffering key, which is used to group similar messages
     * into batches that will then be sent over the wire. The buffer key will be a
     * hash between routing key of the message, the flags and the attributes.
     *
     * @return string
     */
    public function getBufferKey()
    {
        $baseBufferKey = $this->routingKey;
        $baseBufferKey .= (int) $this->flags;

        // start looping through properties recursively.
        $key = $this->hashAttributes($this->attributes);
        $baseBufferKey .= $key;

        // hash the buffer key
        $hashedBufferKey = md5($baseBufferKey);

        return $hashedBufferKey;
    }

    /**
     * Recursively hashes the attributes from the message and returns a key of
     * those messages.
     *
     * @param mixed $attributes
     *
     * @return string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function hashAttributes($attributes)
    {
        if (is_array($attributes)) {
            ksort($attributes);
        }

        $key = "";

        foreach ($attributes AS $attributeKey => $attributeValue) {
            if (is_array($attributeValue)) {
                $key .= $attributeKey;
                $key .= $this->hashAttributes($attributeValue);
            } else {
                $key .= $attributeKey;
                $key .= $attributeValue;
            }
        }

        return $key;
    }

    /**
     * Sets a header for the message
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     *
     * @throws MessageException If the header trying to be set is object|array|resource
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setHeader($key, $value)
    {
        if (is_object($value) || is_array($value) || is_resource($value)) {
            throw new MessageException("Header values cannot be object|array|resources");
        }

        // check if we have the headers
        if (!isset($this->attributes['headers'])) {
            $this->attributes['headers'] = array();
        }

        $this->attributes['headers'][$key] = $value;

        return $this;
    }

    /**
     * Returns the requested header if exists, if not, returns false
     *
     * @param string $key
     *
     * @return mixed|false Returns false if the header is not set
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getHeader($key)
    {
        if (!isset($this->attributes['headers']) || !isset($this->attributes['headers'][$key])) {
            return false;
        }

        return $this->attributes['headers'][$key];
    }
}
