<?php
require_once __DIR__ . "/../../vendor/autoload.php";

function getQueueName()
{
    return "test" . mt_rand(100, 999);
}

$builder = new \Amqp\Builder(__DIR__ . '/fetch-listener.yml');

$message = $builder->getListener()->fetchOne();

print_r($message);