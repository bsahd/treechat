<?php
session_start(['read_and_close' => 1]);
if (!array_key_exists("name", $_SESSION)) {
    http_response_code(401);
    ?>
    not logined
    <?php
    exit;
}

if ($_POST["pass"] == $_POST["pass2"]) {
    $fp = fopen("users.json", "r+");
    if (flock($fp, LOCK_EX)) {  // 排他ロックを確保します
        $users = json_decode(fread($fp, filesize("users.json")), TRUE);
        fseek($fp, 0);
        ftruncate($fp, 0);      // ファイルを切り詰めます
        $users[$_SESSION["name"]]["pw"] = password_hash($_POST["pass"], null);
        fwrite($fp, json_encode($users, JSON_UNESCAPED_UNICODE));
        fflush($fp);            // 出力をフラッシュしてからロックを解放します
        flock($fp, LOCK_UN);    // ロックを解放します
        header("Location: ./logout.php");
    } else {
        echo "ファイルを取得できません!パスワードは変更されていません";
        exit;
    }
    fclose($fp);
} else {
    if ($is_htmx) {
        http_response_code(200);
    } else {
        http_response_code(503);
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ツリーチャット:変更失敗</title>
    </head>

    <body>
        <h1>ツリーチャット:変更失敗</h1>
        <a href="./">戻る</a>
    </body>

    </html>
    <?php

}
