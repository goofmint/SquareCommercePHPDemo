# Square Eコマース APIのPHP実装デモ

これはSquare Connect APIの支払い処理を試すデモプログラムです。PHPのSquare Connectライブラリを使っています。

## セットアップ

### 要件

* PHP >= 5.4.0

### PHPライブラリのインストール法

Square Connectというライブラリが必要です。これは[Composer](https://getcomposer.org/download/)を使ってインストールできます。

`composer.json`があるディレクトリで以下のコマンドを実行します。

```sh
$ php composer.phar install
# または
$ composer install
```

### 変数の置換

`process-card.php` と `sqpaymentform.js` それぞれにアプリケーションIDとアクセストークンを入力する部分があります。これらの情報は Square Developer Portal から取得できます。開発中についてはサンドボックス用を使った方が良いでしょう。

`REPLACE_ME` と書かれている部分を置換してください。

## サンプルの実行

サンプルを実行する場合はこのサンプルのルートディレクトリで以下のコマンドを実行してください。

    php -S localhost:8000

そうすると http://localhost:8000/ にアクセスするとカード入力フォームが表示されるはずです。

もしサンドボックス環境を使っているならば、以下のテスト用カード情報を入力してください。

* カード番号 `4532 7597 3454 5858`
* CVV `111`
* 有効期限は未来の年月であれば何でも
* 郵便番号は適当な7桁の数字

本番環境の情報で試している場合は本当のカード番号を入力してください。実際にチャージされてしまうので注意してください。
