<?php
$nowtime = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']) . " UTC";
session_start(['read_and_close' => 1]);
$fp = fopen("chat.json", "r");
if (flock($fp, LOCK_SH)) {  // 排他ロックを確保します
    $treetext = fread($fp, filesize("chat.json"));
    $tree = json_decode($treetext, TRUE);
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);

if (!array_key_exists("name", $_SESSION)) {
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
    <link rel="stylesheet" href="style.css">
    <script src="index.js"></script>
    <script src="htmx.js"></script>
</head>

<body hx-boost="true" hx-indicator="#loading">
    <div id="loading">
        <div class="progress-bar" id="dialogspin">
            <div class="indeterminate"></div>
        </div>
    </div>
    <h1>ツリーチャット</h1>
    <p>フォーム[表示|<a href="view.php">非表示</a>]</p>
    <p><?= $_SESSION["name"] ?> としてログインしています <a href="logout.php">ログアウト</a> <a
            href="passwd.php">パスワード変更</a></p>
    <p><span hx-get="./checkupdate.php?hash=<?= hash("sha256", $treetext) ?>"
            hx-trigger="load,every 2s"
            hx-indicator="#updateloading"><?= $nowtime ?>時点の情報です</span><span
            id="updateloading">...</span> : <a href="./">再読み込み</a></p>
    <?php
    function createChatTree($root)
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
                    <span id="post-<?= $citem["id"] ?>"><?= $citem["name"] ?>:
                        <?= htmlspecialchars($citem["text"]) ?> - <span
                            id="date-<?= $citem["unixtime"] ?>"><?= date("Y/m/d H:i:s", $citem["unixtime"]) . " UTC" ?></span></span>
                    <script>
                        document.getElementById("date-<?= $citem["unixtime"] ?>").innerText = date2str(new Date(<?= $citem["unixtime"] * 1000 ?>))
                    </script>
                    <form action="remove.php" method="post" style="display:inline;"
                        hx-boost="true">
                        <input type="hidden" name="id" value="<?= $citem["id"] ?>">
                        <input type="submit" value="削除">
                    </form>
                    <?php
                    createChatTree($citem["id"])
                        ?>
                </li>
                <?php
            }
            ?>
            <li>
                <form action="./post.php" method="post">
                    <input type="hidden" name="parent"
                        value="<?= htmlspecialchars($root) ?>">
                    <label>返信:<input type="text" name="text" size="40"
                            autocomplete="off"></label>
                    <input type="submit" value="投稿">
                </form>
            </li>
        </ul>
        <?php
    }
    createChatTree("root")
        ?>
</body>

</html>