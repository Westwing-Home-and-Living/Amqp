<?php
require_once __DIR__ . "/../../vendor/autoload.php";


class Processor implements \Amqp\Handler\HandlerInterface
{
    public function handleMessage(\Amqp\Message\Collection $collection)
    {
        $collection = new \Amqp\Message\Collection();
        return $collection;
    }
}

class ProcessorFailed implements \Amqp\Handler\HandlerFailedInterface
{
    public function handleFailed(\Amqp\Message\Collection $collection)
    {
        print_r($collection);
    }
}

$builder = new \Amqp\Builder(__DIR__ . '/listener.yml');
$listener = $builder->getListener();
$listener->setHandlers(new Processor(), new ProcessorFailed());
$listener->listen();
