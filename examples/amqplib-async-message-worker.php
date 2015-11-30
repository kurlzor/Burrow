#!/usr/bin/php
<?php

if (!isset($argv[1])) {
    $io = fopen('php://stderr', 'w+');
    fwrite($io, "usage: php amqplib-async-message-worker-v2.php <queue-name:string>\n");
    die;
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/base-info.php';



$logger = new \Monolog\Logger('TEST');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output', 0));

$handler = new \Burrow\RabbitMQ\AmqpAsyncHandler($host, $port, $user, $pass, $argv[1]);
$handler->registerConsumer(new \Burrow\Examples\EchoConsumer());
$handler->setLogger($logger);

$worker = new \Burrow\Worker($handler);
$worker->run();