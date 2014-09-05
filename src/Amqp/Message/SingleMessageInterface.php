<?php
namespace Amqp\Message;

interface SingleMessageInterface extends MessageInterface
{
    /**
     * Sets the content of a message
     *
     * @param mixed $content
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setContent($content);

    /**
     * Returns the content of a message
     *
     * @return mixed
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getContent();

    /**
     * Returns the key on which the message can be buffered
     *
     * @return string
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getBufferKey();

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
    public function setHeader($key, $value);

    /**
     * Returns the requested header if exists, if not, returns false
     *
     * @param string $key
     *
     * @return mixed|false Returns false if the header is not set
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function getHeader($key);
}