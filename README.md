coop-notifier
---

http://www.coopdeli.jp/

coopdeli eフレンズの注文状況と翌週の予定をgoogleカレンダーより取得して通知を行うための何かです。

# require

php5.5,6,7あたり。7.1は依存してるguzzleaあたりが動かず。

# installation

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```
# setup config

直下にnotifier.yamlとcredentioal.json（googleカレンダー用）を配置

notifier.yaml

```yaml
coop-user: '組合員コード'
coop-pass: 'パスワード'
calendar_id: '取得するgoogleカレンダーID'
mail-to:
 - yourmail@address.here
mail-from:
  from-email@address.here: "送信者名"
```

# run

run `php notifier.php` or `php notifier-out.php`
