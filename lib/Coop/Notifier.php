<?php
namespace Coop;
class Notifier {
    public static function formatMessage(array $data)
    {
        $body = "";
        $body .= "現在のオーダー状況\n";
        $body .= "------------------------\n";
        $body .= "通常注文：\n";
        foreach($data['standard_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }

        $body .= "自動注文：\n";
        foreach($data['auto_orders'] as $order) {
            $body .= "・{$order['name']}\t{$order['price']}\t{$order['quantity']}個\n";
        }
        return $body;
    }
}