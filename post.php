<?php
session_start(['read_and_close' => 1]);
$is_htmx = isset($_SERVER['HTTP_HX_REQUEST']);
if (!array_key_exists("name", $_SESSION)) {
    if ($is_htmx) {
        http_response_code(200);
    } else {
        http_response_code(401);
    }
    header("Location: ./");
    exit;
}
if ($_POST["text"] == "") {
    if ($is_htmx) {
        http_response_code(200);
    } else {
        http_response_code(404);
    }
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
        <div id="loading">
            <div class="progress-bar" id="dialogspin">
                <div class="indeterminate"></div>
            </div>
        </div>
        <h1>ツリーチャット:エラー</h1>
        <p>投稿が空です。</p>
        <script>window.parent.postMessage('dialogloaded', '*');</script>
        <a href="./">戻る</a>
    </body>

    </html>
    <?php
    exit;
}

$fp = fopen("chat.json", "r+");
if (flock($fp, LOCK_EX)) {  // 排他ロックを確保します
    $tree = json_decode(fread($fp, filesize("chat.json")), TRUE);
    fseek($fp, 0);
    $parentpost = array_values(array_filter($tree, function ($item) {
        return $_POST["parent"] == $item["id"];
    }));
    if (!key_exists(0, $parentpost) && $_POST["parent"] != "root") {
        if ($is_htmx) {
            http_response_code(200);
        } else {
            http_response_code(404);
        }
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
            <div id="loading">
                <div class="progress-bar" id="dialogspin">
                    <div class="indeterminate"></div>
                </div>
            </div>
            <h1>ツリーチャット:エラー</h1>
            <p>投稿が見つかりません</p>
            <script>window.parent.postMessage('dialogloaded', '*');</script>
            <a href="./">戻る</a>
        </body>

        </html>
        <?php
        exit;
    }
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
    header("Location: ./#post-" . $post["id"]);
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);
