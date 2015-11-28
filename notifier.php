<?php
require __DIR__ . "/vendor/autoload.php";
use Symfony\Component\Yaml\Parser;


$config = (new Parser())->parse(file_get_contents(__DIR__ . "/notifier.yaml"));

$coop = new \Coop\Crawler();
$coop->login($config['coop-user'], $config['coop-pass']);
$current_orders = $coop->getCurrentOrder();

//$res = $coop->getClient()->getResponse()->getContent();
//echo $res;

//$client->request('GET', 'https://nb.cws.coop/coopnet/bill/nb/deliveryDetailsListInit.do');

// トップ
//$client->request('GET', 'https://www.cws.coop/coopnet/ec/bb/ecTopInit.do?sid=coopnet01');

//$client->request('GET', 'https://www.cws.coop/coopnet/ec/bb/orderHistoryInit.do?sid=ComEcF00BB010&tcd=tcdcp003');

