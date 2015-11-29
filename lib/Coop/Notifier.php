<?php
namespace Coop;
class Notifier {
    public static function formatMessage($data, $data_prev)
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
        return $body;
    }
}