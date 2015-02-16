```
this project is no longer maintained
```

Purpose
=======

Abstract publishing and listening via an AMQP based broker.

Supported software
==================

All the AMQP based central broker systems should be supported by the current library.
Examples include: RabbitMQ, 0mq, Apache Qpid, StormMq.

Unsuported software
===================

All brokerless systems are not currently supported (0mq brokerless system for example).

Directory structure
===================

- Configuration: holds all the configurable objects related to
    - connection
    - channel
    - exchange
    - queue
    - other publisher/listener settings

    The configuration relies on Symfony DI and other packages

- Exception: holds all the local specific exception handling code, containing a few
  specialized exception classes
    - ConfigurationException: all exceptions related to configuration values
    - MessageException: all exceptions related to message values
    - PublisherException: all exceptions related to publishing messages

- Listener: holds the current implementation of a listener. Provides two methods for
  retrieving messages from messaging queue: get and listen.

- Message: provides implementation for two types of messages: single message and
  collection

- Publisher: provides implementation for two types of publishing:
    - Publisher: simple publisher, allows pushing one message at a time
    - Buffered: buffered publisher, will no publish every message on the queue, but
    will attempt to buffer the messages based on some limits and will publish batches 
    of messages.

Publisher implementations
-------------------------

There are two types of publishers available in the current implementation of the library:

1. Publisher
    It's a simple publisher allowing the publish of one message at a time.

    In order to provide compatibility between multiple publisher implementations, even
    in the case of only one message, the publisher will need to push a collection of
    one item.

2. Buffered
    A more complex implementation of the publisher.

    Since RabbitMq and other brokers deal much better with larger messages than with more
    smaller ones, this publisher will attempt to buffer the messages based on the message 
    attributes and routing keys.

    Once the buffers reach a limit, than they will be discarded and pushed remotely with 
    what they contain at that point.

    The resulting message collection will borrow all the attributes and the routing key 
    of the messages  being buffered, so when unpacked the messages will contain the same
    attributes as when they were sent.

Listener implementation
-----------------------

The listener has a simple implementation exposing three public methods:

1. setCallable
    This method is used in conjunction with listen (see below).

    It sets a messageHandler for to be used with listen, which in turn will call it whenever messages
    have to be dispatched.

    The messageHandler must return a boolean value indicating completion in case of true and failure
    in case of false.

    If the messageHandler returns false, listen will break from the listening cycle and unacknowledge
    the last message parsed, so it will get processed again on the next run.

2. fetchOne
    This method allows fetching of one message from the messaging queue, and then it will
    return to the caller.

    The method returns a collection of messages, that can be iterated through, even if there is 
    only one message that was pushed.

    setCallable is not a requirement for this method.

    ATTENTION: This method will automaticly acknowledge the message as being processed, so it needs
    to be used with care. 

    Also, this method operates on listening in a while cycle, so potentially is consuming some resources.

    Therefore, it is not recommended to be used.

3. listen
    This method listens continuously for incoming messages.

    Relies on the server pushing the messages towards the current client and dispatches the message
    onto a user defined callback that needs to be passed in by the user.

    For more details about the requirements about the callback, please see @setCallable.
