<?php
namespace Amqp\Message;

class Collection extends AbstractCollection implements MessageInterface
{
    /**
     * The current routing key for the messages contained inside
     *
     * @var null|string
     */
    protected $routingKey;

    /**
     * The flags that apply for the stored set of messages
     *
     * @var int
     */
    protected $flags = AMQP_NOPARAM;

    /**
     * The current attributes that apply for the list of messages contained
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Sets the general flags for the messages encapsulated in here
     *
     * @param $flags
     *
     * @return $this
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setFlags($flags = AMQP_NOPARAM)
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * Returns the current message flags
     *
     * @return int
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Sets the attributes for the messages encapsulated in here
     *
     * @param array $attributes
     *
     * @return $this
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Returns the current attributes
     *
     * @return array
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the routing key for the messages encapsulated in here
     *
     * @param $routingKey
     *
     * @return $this
     *
     * @throws \Amqp\Exception\MessageException If the routing key is object|resource
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setRoutingKey($routingKey = null)
    {
        if (is_object($routingKey) || is_resource($routingKey)) {
            throw new \Amqp\Exception\MessageException(
                "The routing key cannot be an object or resource!"
            );
        }
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * Return the routing key
     *
     * @return null|string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * Serialize the needed items in json format.
     *
     * @return string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function serialize()
    {
        $serialized = json_encode($this->internalArray);
        return $serialized;
    }

    /**
     * Unserialize the needed items and reallocate them where they should be
     *
     * @param string $serialized
     *
     * @throws \Amqp\Exception\MessageException
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function unserialize($serialized)
    {
        // unserialize to array
        $unserialized = json_decode($serialized, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Amqp\Exception\MessageException(
                "Invalid json serialization for the messages."
            );
        }

        $this->internalArray = $unserialized;
    }

    /**
     * Returns the string representation of the current array
     *
     * @return string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * Sets the content of a collection
     *
     * @param mixed $content
     *
     * @return bool|void
     *
     * @throws \Amqp\Exception\MessageException
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setContent($content)
    {
        if (!is_array($content)) {
            throw new \Amqp\Exception\MessageException("A collection content needs to be an array");
        }

        $this->internalArray = $content;
    }

    /**
     * Returns the content of a collection in the form of an array of multiple
     * messages
     *
     * @return array|mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getContent()
    {
        return $this->internalArray;
    }

    /**
     * Sets a value in the array
     *
     * @param mixed $offset  The offset
     * @param mixed $message The value
     *
     * @return null
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function offsetSet($offset, $message)
    {
        if (is_null($offset)) {
            $this->internalArray[] = $message;
        } else {
            $this->internalArray[$offset] = $message;
        }
    }

    /**
     * Returns the value of the current offset
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @throws \OutOfBoundsException If the current offset does not exist
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function offsetGet($offset)
    {
        // check if the offset does exist
        if ($this->offsetExists($offset) === false) {
            throw new \OutOfBoundsException(
                "Requested offset does not exist (" . $offset . ")"
            );
        }

        // create the message to be returned
        $message = new \Amqp\Message\Message($this->internalArray[$this->position]);
        $message->setAttributes($this->getAttributes());

        return $message;
    }

    /**
     * Returns the current element on that position
     *
     * @return mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function current()
    {
        $message = $this->internalArray[$this->position];

        // we have a native message, return it
        if ($message instanceof SingleMessageInterface) {
            return $message;
        }

        // create the message to be returned
        $message = new \Amqp\Message\Message($message);
        $message->setAttributes($this->getAttributes());

        return $message;
    }
}