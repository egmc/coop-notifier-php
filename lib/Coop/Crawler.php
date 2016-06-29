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

    const LOGIN_URL = 'https://ec.coopdeli.jp/auth/login.html';

    const CURRENT_ORDER_URL = 'https://weekly.coopdeli.jp/order/index.html';

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

        $form = $crawler->filter('#WCSBFE15')->form();

        $params = [
            'j_username' => $user,
            'j_password' => $pass,
   //         'from' => 'coopnet-ec',
            'PageID' => 'WCSBFE15',
    //        'tcd' => 'tcd001'
        ];

        $this->client->submit($form, $params);

        return $this;
    }

    public function getCurrentOrder()
    {

        $this->crawler = $this->client->request('GET', self::CURRENT_ORDER_URL);
        //echo $this->crawler->filter('#accountUser')->text();
        $deadline_date = $this->crawler->filter('div#cartOrderStatus div dl dd')->first()->text();
        $delivery_expected_date = $this->crawler->filter('div#cartOrderStatus div dl dd')->eq(2)->text();
        //echo $deadline_date;
        //echo $delivery_expected_date;


        $standard_orders = $this->getOrder('normal');
        //$auto_orders =  $this->getOrder('auto_order');
        $auto_orders = [];
        //var_dump($standard_orders);
        return [
            "deadline_date" => $deadline_date,
            "delivery_expected_date" => $delivery_expected_date,
            "standard_orders" => $standard_orders,
            "auto_orders" => $auto_orders,
        ];
    }

    protected function getOrder($parent, $previous = false)
    {
        if (!$this->crawler->filter("table.{$parent} tbody")->first()) {
            return [];
        }
        $nodes = $this->crawler->filter("table#wecpwa0010_{$parent} tbody tr");
        //var_dump($nodes);
        return $this->crawler->filter("table#wecpwa0010_{$parent} tbody tr")->reduce(function($node) {
            $item = $node->filter('tr.cartSubs');
            return (boolean)$item->getNode(0) === false;
        })->each(function($node) use ($previous) {
            $item = [
                'name' => '',
                'price' => '',
                'quantity' => '',
            ];

            $item['name'] = $node->filter('td.cartItemDetail p')->text();
            $item['price'] = $node->filter('td.cartItemPrice')->text();
            $item['price'] = trim($item['price']);
            if (!$previous) {
                $item['quantity']  = (int)$node->filter('.cartItemQty input')->attr('value');
            } else {
                $item['quantity']  = (int)$node->filter('.cartItemQty')->text();
            }
            return $item;
        });
    }

    public function getPreviousOrder()
    {
        $delivery_expected_date ='';
        $standard_orders = $auto_orders = [];
        $prev = '';

        $this->crawler = $this->client->request('GET', self::CURRENT_ORDER_URL);

        $form = $this->crawler->filter('#WECPWA0010')->form();

        $current = $this->crawler->filter('.weekOrderSelect select option')->reduce(function($node){
            return $node->attr('selected') == 'selected';
        })->attr('value');

        $prev = $this->crawler->filter('.weekOrderSelect select option')->each(function($node) use ($current) {
            if ($node->attr('value') < $current) {
                return $node->attr('value');
            }
        });
        $prev = array_values(array_filter($prev))[0];

        $form->disableValidation()->setValues(['osk'=> $prev, 'curosk' => $prev, 'odc' => $form->getValues()['curodc']]);

        $this->crawler = $this->client->submit($form);


        $delivery_expected_date = $this->crawler->filter('div#cartOrderStatus div dl dd')->eq(2)->text();

//        $this->randomSleep();
        $standard_orders = $this->getOrder('normal', true);
        //$auto_orders =  $this->getOrder('auto_order', true);
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