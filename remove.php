<?php
session_start(['read_and_close' => 1]);
if (!array_key_exists("name", $_SESSION)) {
?>
    not logined
    <?php
    exit;
}
$fp = fopen("chat.json", "r+");
if (flock($fp, LOCK_EX)) {  // 排他ロックを確保します
    $tree = json_decode(fread($fp, filesize("chat.json")), TRUE);
    fseek($fp, 0);
    $post = array_values(array_filter($tree, function ($item) {
        return $_POST["id"] == $item["id"];
    }));
    if (!key_exists(0, $post)) {
        http_response_code(404);
    ?>
        post not found
        <a href="./">back</a>
    <?php
        exit;
    }
    if ($post[0]["name"] != $_SESSION["name"]) {
        http_response_code(403);
    ?>
        ユーザーが違うため、削除できません
        <a href="./">back</a>
    <?php
        exit;
    }
    $child = array_filter($tree, function ($item) {
        return $_POST["id"] == $item["parent"];
    });
    if (count($child) != 0) {
        http_response_code(503);
    ?>
        小要素があるため、削除できません
        <a href="./">back</a>
<?php
        exit;
    }
    $tree = array_values(array_filter($tree, function ($item) {
        return $_POST["id"] != $item["id"];
    }));
    ftruncate($fp, 0);      // ファイルを切り詰めます
    fwrite($fp, json_encode($tree, JSON_UNESCAPED_UNICODE));
    fflush($fp);            // 出力をフラッシュしてからロックを解放します
    header("Location: ./");
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);
