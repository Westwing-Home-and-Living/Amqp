<?php

namespace Amqp\Listener;

use Amqp\Amqp;
use Amqp\Exception\MessageException;
use Amqp\Handler\HandlerFailedInterface;
use Amqp\Handler\HandlerInterface;
use Amqp\Message\Collection;

class Listener extends Amqp
{
    /**
     * The user provided messageHandler
     * @var HandlerInterface
     */
    protected $messageHandler;

    /**
     * Handles all the failed messages
     *
     * @var HandlerFailedInterface
     */
    protected $failedMessageHandler;

    /**
     * @var \AMQPQueue
     */
    protected $queue;

    /**
     * @param \AMQPQueue $queue
     *
     */
    public function __construct(\AMQPQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns a single message from the list of messages in the queue
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function fetchOne()
    {
        while (true) {
            $message = $this->queue->get(AMQP_NOPARAM);

            // we got one message
            if ($message instanceof \AMQPEnvelope) {
                $collection = $this->processReceivedMessage($message);

                $this->queue->ack($message->getDeliveryTag());

                return $collection;
            }
        }
    }

    /**
     * Consume continuously
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function listen()
    {
        $this->queue->consume(array($this, 'internalSolver'));
    }


    /**
     * Internal dispatcher: does all the conversion to the collection object and
     * acknowledges or unacknowledges the message for the user.
     *
     * @param \AMQPEnvelope $message
     * @param \AMQPQueue $queue
     *
     * @return bool
     *
     * @throws MessageException If the messageHandler is not HandlerInterface
     * @throws MessageException If the failed message handler does not implement HandlerFailedInterface
     * @throws MessageException If the response from the handler is not returning a Collection
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function internalSolver(\AMQPEnvelope $message, \AMQPQueue $queue)
    {
        $collection = $this->processReceivedMessage($message);
        $collection->setRoutingKey($message->getRoutingKey());

        if (!$this->messageHandler instanceof HandlerInterface) {
            throw new MessageException("Listening messageHandler provided is not messageHandler");
        }

        if (!$this->failedMessageHandler instanceof HandlerFailedInterface) {
            throw new MessageException("The failed message handler provided is not a failedHandler");
        }

        /** @var Collection $result */
        $result = $this->messageHandler->handleMessage($collection);

        $queue->ack($message->getDeliveryTag());

        if (!$result instanceof Collection) {
            throw new MessageException("A collection must be returned by the processor of the message");
        }

        if ($result->count() > 0) {
            $this->failedMessageHandler->handleFailed($result);
        }

        // do not stop execution on failed message, pass the message to the failed driver
        return true;
    }

    /**
     * Process the received message in a unitary manner
     *
     * @param \AMQPEnvelope $message
     *
     * @return Collection
     *
     * @throws MessageException
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    protected function processReceivedMessage(\AMQPEnvelope $message)
    {
        // we will always get a collection here, so we need to process the collection
        $collection = new Collection();

        // decode the json we have received
        $jsonReceived = json_decode($message->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MessageException("Json error encountered while processing message: " . $message->getBody());
        }

        $collection->setAttributes(
            array(
                'headers' => $message->getHeaders(),
                'type' => $message->getType(),
            )
        );

        // add the messages to the list of needed messages
        foreach ($jsonReceived as $messageReceived) {
            $collection->offsetSet(null, $messageReceived);
        }

        return $collection;
    }

    /**
     * Sets the current handlers for the message, both for the received messages and the failed ones.
     *
     * @param HandlerInterface $messageHandler
     * @param HandlerFailedInterface $failedMessageHandler
     *
     * @return void
     *
     * @author Cristian Datculescu <cristian.datculescu@westwing.de>
     */
    public function setHandlers(
        HandlerInterface $messageHandler,
        HandlerFailedInterface $failedMessageHandler
    ) {
        $this->messageHandler = $messageHandler;
        $this->failedMessageHandler = $failedMessageHandler;
    }
}
