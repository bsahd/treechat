<?php
if (!($_SERVER['REQUEST_METHOD'] == "POST")) {
  http_response_code(400)
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ツリーチャット:ログアウト失敗</title>
  </head>

  <body>
    <h1>ツリーチャット:ログアウト失敗</h1>
    <p>ログアウトはPOSTメソッドで実行してください</p>
    <a href="./">戻る</a>
  </body>

  </html>
  <?php
  exit;
}
session_start();
$_SESSION = array();
session_destroy();
header("Location: ./");
