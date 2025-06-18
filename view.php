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
    <script>
        var CHAT_HASH = "<?= hash("sha256", $treetext) ?>"
    </script>
    <script src="view.js"></script>
    <script src="htmx.js"></script>
</head>

<body>
    <div id="loading">
        <div class="progress-bar" id="dialogspin">
            <div class="indeterminate"></div>
        </div>
    </div>
    <h1>ツリーチャット</h1>
    <p>フォーム[<a href="./">表示</a>|非表示]</p>
    <p><?= $_SESSION["name"] ?> としてログインしています <a href="logout.php">ログアウト</a> <a
            href="passwd.php">パスワード変更</a></p>
    <p id="updateStatus"><?= $nowtime ?>時点の情報です</p>
    <dialog id="fsenddialog">
        <button onclick="document.getElementById('fsenddialog').close()"><img
                src="close.png" alt="閉じる"></button>
        <div class="progress-bar" id="dialogspin">
            <div class="indeterminate"></div>
        </div>
        <iframe src="about:blank" frameborder="0" name="formsend"
            sandbox="allow-scripts"></iframe>
    </dialog>
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
                    <?php
                    createChatTree($citem["id"])
                        ?>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }
    createChatTree("root")
        ?>
</body>

</html>