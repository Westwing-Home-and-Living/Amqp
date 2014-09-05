<?php

require_once __DIR__ . "/../../vendor/autoload.php";

$builder = new \Amqp\Builder(__DIR__ . '/simple-publisher.yml');

$message = new \Amqp\Message\Message("Test message!");
$message->setType("delete");

$builder->getPublisher()->publish($message);
