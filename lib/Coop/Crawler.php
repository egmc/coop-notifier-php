<?php
namespace Coop;
use Goutte\Client;

/**
 * Class Crawler
 *
 * @package Coop
 */
class Crawler {

    const UA = 'Mozilla/5.0 (Android; Linux armv7l; rv:9.0) Gecko/20111216 Firefox/9.0 Fennec/9.0';

    const LOGIN_URL = 'https://www.cws.coop/coopnet/auth/bb/login.do?from=coopnet-ec';

    const CURRENT_ORDER_URL = 'https://www.cws.coop/coopnet/ec/bb/orderListDetailInit.do?sid=ComEcF00BB010&tcd=tcdcp005';

    const PREVIOUS_ORDER_URL = 'https://www.cws.coop/coopnet/ec/bb/orderHistoryInit.do?sid=ComEcF02BB010&tcd=tcdcp003';

    protected $crawler;
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setHeader('User-Agent', self::UA);
    }

    public function login($user, $pass)
    {
        $crawler = $this->client->request('GET', self::LOGIN_URL);

        $form = $crawler->selectButton('ログイン')->form();

        $params = [
            'j_username' => $user,
            'j_password' => $pass,
            'from' => 'coopnet-ec',
            'sid' => 'ComAuthF01BB015',
            'tcd' => 'tcd001'
        ];

        $this->client->submit($form, $params);

        return $this;
    }

    public function getCurrentOrder()
    {
        $this->randomSleep();

        $this->crawler = $this->client->request('GET', self::CURRENT_ORDER_URL);

        $deadline_date = $this->crawler->filter('div#close_date_pane  dd')->first()->text();
        $delivery_expected_date = $this->crawler->filter('div#close_date_pane  dd.delivery')->first()->text();

        $standard_orders = $this->getOrder('standard');
        $auto_orders =  $this->getOrder('auto_order');
        return [
            "deadline_date" => $deadline_date,
            "delivery_expected_date" => $delivery_expected_date,
            "standard_orders" => $standard_orders,
            "auto_orders" => $auto_orders,
        ];
    }

    protected function getOrder($parent, $previous = false)
    {
        if (!$this->crawler->filter("table.{$parent} tr")->first()) {
            return [];
        }
        return $this->crawler->filter("table.{$parent} tr")->reduce(function($node){
            $item = $node->filter('td.order_clm p.name_clm');
            return (boolean)$item->getNode(0);
        })->each(function($node) use ($previous) {
            $item = [
                'name' => '',
                'price' => '',
                'quantity' => '',
            ];

            $item['name'] = $node->filter('td.order_clm p.name_clm')->text();
            $item['price'] = $node->filter('td.price_clm')->text();
            $item['price'] = trim($item['price']);
            if (!$previous) {
                $item['quantity']  = (int)$node->filter('.quantity')->attr('value');
            } else {
                $item['quantity']  = (int)$node->filter('.quantity_clm')->text();
            }
            return $item;
        });
    }

    public function getPreviousOrder()
    {
        $this->randomSleep();

        $this->crawler = $this->client->request('GET', self::PREVIOUS_ORDER_URL);

        $delivery_expected_date = $this->crawler->filter('div.shipping_state .delivery')->text();
        $delivery_expected_date = trim(preg_replace('/(\s)+※.+/' ,'', $delivery_expected_date));
        $delivery_expected_date = str_replace('お届け予定日：', '', $delivery_expected_date);
        $standard_orders = $this->getOrder('standard', true);
        $auto_orders =  $this->getOrder('auto_order', true);
        return [
            "delivery_expected_date" => $delivery_expected_date,
            "standard_orders" => $standard_orders,
            "auto_orders" => $auto_orders,
        ];
    }


    public function getClient()
    {
        return $this->client;
    }

    protected function randomSleep()
    {
        sleep(rand(1,3));
    }
}