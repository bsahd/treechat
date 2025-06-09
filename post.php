<?php
session_start(['read_and_close' => 1]);
$style = $_POST["style"] ?? "";
if (!array_key_exists("name", $_SESSION)) {
    ?>
    not logined
    <?php
    exit;
}
if ($_POST["text"] == "") {
    ?>
    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ツリーチャット:エラー</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <h1>ツリーチャット:エラー</h1>
        <p>投稿が空です。</p>
        <?php
        if ($style == "dialog") {
            ?><button type="button"
                onclick="window.parent.postMessage('closedialog', '*');">close</button><?php
        } else {
            ?><a href="./">戻る</a><?php
        }
        ?>
    </body>

    </html>
    <?php
    exit;
}

$fp = fopen("chat.json", "r+");
if (flock($fp, LOCK_EX)) {  // 排他ロックを確保します
    $tree = json_decode(fread($fp, filesize("chat.json")), TRUE);
    fseek($fp, 0);
    ftruncate($fp, 0);      // ファイルを切り詰めます
    $post = [
        "id" => uniqid("", true),
        "parent" => $_POST["parent"],
        "text" => $_POST["text"],
        "unixtime" => time(),
        "name" => $_SESSION["name"]
    ];
    array_push($tree, $post);
    fwrite($fp, json_encode($tree, JSON_UNESCAPED_UNICODE));
    fflush($fp);            // 出力をフラッシュしてからロックを解放します
    flock($fp, LOCK_UN);    // ロックを解放します
    if ($style == "dialog") {
        ?>完了
        <script>window.parent.postMessage('reload', '*');</script>
        <?php
    } else {
        header("Location: ./");
    }
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);
