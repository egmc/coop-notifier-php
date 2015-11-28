<?php
require __DIR__ . "/vendor/autoload.php";
use Symfony\Component\Yaml\Parser;


$config = (new Parser())->parse(file_get_contents(__DIR__ . "/notifier.yaml"));

$coop = new \Coop\Crawler();
$coop->login($config['coop-user'], $config['coop-pass']);
$current_orders = $coop->getCurrentOrder();

$mailer = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
$message = \Swift_Message::newInstance();
$message->setTo('eg2mix@gmail.com');
$message->setFrom('coop@eg2mix.com');
$message->setSubject('コープさんレター');
$message->setBody(\Coop\Notifier::formatMessage($current_orders));

$mailer->send($message);
