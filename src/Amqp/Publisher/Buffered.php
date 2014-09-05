<?php

namespace Amqp\Publisher;

use Amqp\Amqp;
use Amqp\Message\Collection;
use Amqp\Message\SingleMessageInterface;
use Amqp\Exception\PublisherException as PubException;

class Buffered extends Amqp implements PublisherInterface
{
    /**
     * The message buffers
     *
     * @var array
     */
    protected $buffers = array();

    /**
     * The buffer size. Defaults to 100 messages
     *
     * @var int
     */
    protected $bufferSize = 100;

    /**
     * Sets the current allowed buffer size. The buffer size needs to be an integer
     * greater than 1
     *
     * @param int $size
     *
     * @throws PubException
     * @return  $this
     */
    public function setBufferSize($size)
    {
        if (!is_numeric($size) || $size <= 1) {
            throw new PubException(
                "The buffer size needs to be a positive integer greater than 1"
            );
        }

        $this->bufferSize = $size;

        return $this;
    }

    /**
     * Publish a message over the messaging queue. Since this is a buffered
     * implementation, this means that the message can be published at a later
     * point because it will be buffered. The emptying of the channel is only
     * triggered at the moment we have all the buffers filled or when the object
     * is destroyed, which triggers the publishing of all the messages in a batch
     *
     * @param SingleMessageInterface $message
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function publish(SingleMessageInterface $message)
    {
        $this->bufferMessage($message);
        $this->cleanBuffers();
    }

    /**
     * Cleans the current existing buffers. In normal conditions ($forceCleanup = false)
     * this means that only the full buffers will be cleaned. In the case of the
     * forced cleanup (for example on destruct), the buffers will be emptied
     * and sent over so there are no messages lost.
     *
     * @param bool $forceCleanup
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function cleanBuffers($forceCleanup = false)
    {
        // this option should only be enabled on destruct
        foreach ($this->buffers AS $key => $buffer) {
            if ($forceCleanup === true || count($buffer) === $this->bufferSize) {
                $this->push($buffer);

                // unset the current buffer
                unset($this->buffers[$key]);
            }
        }
    }

    /**
     * Adds the message to the buffer
     *
     * @param SingleMessageInterface $message the message to be buffered
     *
     * @return bool
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function bufferMessage(SingleMessageInterface $message)
    {
        $bufferKey = $message->getBufferKey();

        if (!isset($this->buffers[$bufferKey])) {
            // allocate all the message properties to the needed buffer so that
            // we know for sure all the messages in that buffer will have the
            // exact same characteristics.
            $bufferObject = new Collection();
            $bufferObject->setRoutingKey($message->getRoutingKey())
                ->setFlags($message->getFlags())
                ->setAttributes($message->getAttributes());
            $this->buffers[$bufferKey] = $bufferObject;
        }

        $this->buffers[$bufferKey][] = $message->getContent();

        return true;
    }

    /**
     * Sends all the messages currently residing in the buffers to remote AMQP
     * broker so that we do not lose any messages that are remaining.
     */
    public function __destruct()
    {
        $this->cleanBuffers(true);
    }
}
