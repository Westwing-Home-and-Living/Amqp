<?php
require_once __DIR__ . "/../../vendor/autoload.php";

$builder = new \Amqp\Builder(__DIR__ . '/buffered-publisher.yml');

for ($i = 0; $i < 10; $i++) {
    // create the message
    $message = new \Amqp\Message\Message("Test message " . $i);
    $type = mt_rand(0, 1) == 1 ? "delete" : "post";
    $message->setType($type);

    // publish it.
    $builder->getPublisher()->publish($message);
}


