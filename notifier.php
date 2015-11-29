<?php
require __DIR__ . "/vendor/autoload.php";
use Symfony\Component\Yaml\Parser;


$config = (new Parser())->parse(file_get_contents(__DIR__ . "/notifier.yaml"));

$coop = new \Coop\Crawler();
$coop->login($config['coop-user'], $config['coop-pass']);
$current_orders = $coop->getCurrentOrder();
$previous_orders = $coop->getPreviousOrder();

$mailer = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
$message = \Swift_Message::newInstance();
$message->setTo($config['mail-to']);
$message->setFrom($config['mail-from']);
$message->setSubject('コープさん便り');
$message->setBody(\Coop\Notifier::formatMessage($current_orders, $previous_orders));

$mailer->send($message);
