<?php
date_default_timezone_set("Asia/Tokyo");
$nowtime = date("Y/m/d H:i:s") . "(JST)";
session_start(['read_and_close'=>1]);
$fp = fopen("chat.json", "r");
if (flock($fp, LOCK_SH)) {  // 排他ロックを確保します
    $tree = json_decode(fread($fp, filesize("chat.json")), TRUE);
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);

if(!array_key_exists("name",$_SESSION)){
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ツリーチャット</title>
</head>

<body>
    <h1>ツリーチャット</h1>
    <p><?= $_SESSION["name"]?> としてログインしています <a href="logout.php">ログアウト</a></p>
    <p><?= $nowtime ?>時点の情報です</p>
    <?php
    function generateHTML($root)
    {
    ?>
        <ul>
            <?php
            global $tree;
            $children = array_filter($tree, function ($item) use ($root) {
                return $item["parent"] == $root;
            });
            foreach ($children as $citem) {
            ?>
                <li>
                    <!-- <?= $citem["id"] ?> -->
                    <?= $citem["name"] ?>: <?= htmlspecialchars($citem["text"]) ?> - <?= $citem["date"] ?>
                    <?php
                    if($citem["name"] == $_SESSION["name"]){
                        ?>
                        <form action="remove.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $citem["id"]?>">
                            <input type="submit" value="削除">
                        </form>
                        <?php
                    }
                    generateHTML($citem["id"])
                    ?>
                </li>
            <?php
            }
            ?>
            <li>
                <form action="./post.php" method="post">
                    <input type="hidden" name="parent" value="<?= htmlspecialchars($root) ?>">
                    <label>返信:<input type="text" name="text" size="40"></label>
                    <input type="submit" value="投稿">
                </form>
            </li>
        </ul>
    <?php
    }
    generateHTML("root")
    ?>
</body>

</html>