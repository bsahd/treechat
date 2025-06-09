<?php
$users = json_decode(file_get_contents("users.json"), TRUE);
session_start();
session_regenerate_id(true);
if (
    isset($users[$_POST["name"]]) &&
    $users[$_POST["name"]] == "" && $_POST['pass'] == ""
) {
    $_SESSION['name'] = $_POST["name"];
    header("Location: ./passwd.php");
    exit;
}
if (isset($users[$_POST["name"]]) && password_verify($_POST['pass'], $users[$_POST["name"]])) {
    $_SESSION['name'] = $_POST["name"];
    header("Location: ./");
} else {
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ツリーチャット:ログイン失敗</title>
    </head>

    <body>
        <h1>ツリーチャット:ログイン失敗</h1>
        <a href="./">戻る</a>
    </body>

    </html>
    <?php

}
