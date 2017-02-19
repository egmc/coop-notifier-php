<?php
namespace Coop;

use Google_Client;
use Google_Service_Calendar;
use DateTime;

/**
 * Class Calendar
 *
 * @package Coop
 */
class Calendar {

    protected $service;

    protected $calendar_id;

    public function __construct($credntial_path, $calendar_id)
    {
        $scopes = ['https://www.googleapis.com/auth/calendar'];

        $this->calendar_id = $calendar_id;

        $client = new Google_Client();
        $client->setAuthConfigFile($credntial_path);
        $client->setApplicationName("coop-notifier");
        $client->setScopes($scopes);

        $client->useApplicationDefaultCredentials();
        $this->service = new Google_Service_Calendar($client);
    }

    public function getEvents()
    {
        $ret = [];

        $date_from = (new DateTime)->modify("Monday next week")->format(DateTime::RFC3339);
        $date_to = (new DateTime($date_from))->modify("Tuesday next week")->format(DateTime::RFC3339);

        $events = $this->service->events->listEvents($this->calendar_id, [
            'timeMin' => $date_from,
            'timeMax' => $date_to,
            'singleEvents' =>true,
            'orderBy' => 'startTime',

        ]);
        foreach ($events as $event) {
            $item = [
                'title' => '',
                'start' => '',
                'end' => '',
            ];
            $item['title'] = $event->summary;
            $start = isset($event->start['date']) ? $event->start['date'] : $event->start['dateTime'];
            $end = isset($event->end['date']) ? $event->end['date'] : $event->end['dateTime'];

            $item['start'] = (new DateTime($start))->format('m/d H:i');
            $item['end'] = (new DateTime($start))->format('m/d H:i');
            $ret[] = $item;
        }
        return $ret;
    }


}