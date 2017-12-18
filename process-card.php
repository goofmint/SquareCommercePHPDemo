<?php

require 'vendor/autoload.php';

# このアクセストークンはSquareへAPIアクセスするのに使われます。
# 開発、テスト中はサンドボックスのアクセストークンを使ってください。
$access_token = 'REPLACE_ME';

# フォームから送信されていない場合はこちら
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  error_log("Received a non-POST request");
  echo "Request not allowed";
  http_response_code(405);
  return;
}

# nonceがない場合はエラー
$nonce = $_POST['nonce'];
if (is_null($nonce)) {
  echo "Invalid card data";
  http_response_code(422);
  return;
}

\SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($access_token);
$locations_api = new \SquareConnect\Api\LocationsApi();

try {
  $locations = $locations_api->listLocations();
  # カード処理が実行できる店舗情報を取得します
  $location = current(array_filter($locations->getLocations(), function($location) {
    $capabilities = $location->getCapabilities();
    return is_array($capabilities) &&
      in_array('CREDIT_CARD_PROCESSING', $capabilities);
  }));

} catch (\SquareConnect\ApiException $e) {
  echo "Caught exception!<br/>";
  print_r("<strong>Response body:</strong><br/>");
  echo "<pre>"; var_dump($e->getResponseBody()); echo "</pre>";
  echo "<br/><strong>Response headers:</strong><br/>";
  echo "<pre>"; var_dump($e->getResponseHeaders()); echo "</pre>";
  exit(1);
}

$transactions_api = new \SquareConnect\Api\TransactionsApi();

$request_body = array (
  "card_nonce" => $nonce,
  # 決済額について。このコードは100円固定になっています。
  "amount_money" => array (
    "amount" => 100,
    "currency" => "JPY"
  ),

  # すべての決済処理についてSDKはユニークなIDが必要です。
  # もし課金成功したか分からない場合は、同じユニークなIDを使えますので、重複課金を防げます。
  "idempotency_key" => uniqid()
);

# 決済処理が失敗した場合、SDKは例外処理を返します。うまくいった場合には2xx系のステータスを返します
try {
  $result = $transactions_api->charge($location->getId(), $request_body);
  echo "<pre>";
  print_r($result);
  echo "</pre>";
} catch (\SquareConnect\ApiException $e) {
  echo "Caught exception!<br/>";
  print_r("<strong>Response body:</strong><br/>");
  echo "<pre>"; var_dump($e->getResponseBody()); echo "</pre>";
  echo "<br/><strong>Response headers:</strong><br/>";
  echo "<pre>"; var_dump($e->getResponseHeaders()); echo "</pre>";
}
