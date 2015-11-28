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

    }

    public function getPreviousOrder()
    {

    }

    public function getClient() {
        return $this->client;
    }
}