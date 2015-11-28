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

    const PREVIOUS_ORDER_URL = 'https://nb.cws.coop/coopnet/bill/nb/deliveryDetailsListInit.do';

    protected $crawler;
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setHeader('User-Agent', self::UA);
    }

    public function login ($user, $pass)
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
        $this->crawler = $this->client->request('GET', self::CURRENT_ORDER_URL);
        //$res = $this->crawler->filter('table.standard > tr > td.order_clm')->text();
        $res = $this->crawler->filter('table.standard > tr')->each(function($node){
            $item = $node->filter('td.order_clm p.name_clm');
            if ($item->getNode(0)) {
//                var_dump($item->text());
                return [$item->text()];
            }
        });

        var_dump($res);
    }

    public function getPreviousOrder()
    {

    }

    public function getClient() {
        return $this->client;
    }
}