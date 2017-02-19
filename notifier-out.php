<?php
require __DIR__ . "/vendor/autoload.php";
use Symfony\Component\Yaml\Parser;


$config = (new Parser())->parse(file_get_contents(__DIR__ . "/notifier.yaml"));

$coop = new \Coop\Crawler();
$coop->login($config['coop-user'], $config['coop-pass']);
$current_orders = $coop->getCurrentOrder();
$previous_orders = $coop->getPreviousOrder();

$cal = new \Coop\Calendar(__DIR__ . "/credential.json", $config['calendar_id']);
$events = $cal->getEvents();

echo \Coop\Notifier::formatMessage($current_orders, $previous_orders, $events);
