<?php
namespace Coop;
class Notifier {

    const ORDER_URL = 'https://weekly.coopdeli.jp/index.html';

    public static function formatMessage($data, $data_prev, $events)
    {
        $body = "";

        $body .= "今の注文状況だよ\n";
        $body .= "------------------------\n";
        $body .= "締切日時: {$data['deadline_date']}\n";
        $body .= "配達予定日: {$data['delivery_expected_date']}\n";
        $body .= "\n";
        $body .= "通常注文：\n";
        foreach($data['standard_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }

        $body .= "自動注文：\n";
        foreach($data['auto_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }

        $body .= "\n";
        $body .= "前回の注文状況だよ\n";
        $body .= "------------------------\n";
        $body .= "配達予定日: {$data_prev['delivery_expected_date']}\n";
        $body .= "\n";
        $body .= "通常注文：\n";
        foreach($data_prev['standard_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }

        $body .= "自動注文：\n";
        foreach($data_prev['auto_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }
        $body .= "\n";
        $body .= "予定はこんな感じ\n";
        $body .= "------------------------\n";
        foreach($events as $event) {
            $body .= "・{$event['title']} {$event['start']} - {$event['end']}\n";
        }
        $body .= "\n";
        $body .= "注文する？\n";
        $body .= self::ORDER_URL;

        return $body;
    }
}