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
    <script src="index.js"></script>
</head>

<body>
    <h1>ツリーチャット</h1>
    <p><?= $_SESSION["name"] ?> としてログインしています <a href="logout.php">ログアウト</a> <a
            href="passwd.php">パスワード変更</a></p>
    <p id="updateStatus"><?= $nowtime ?>時点の情報です</p>
    <dialog id="fsenddialog">
        <button
            onclick="document.getElementById('fsenddialog').close()"><img src="close.png" alt="閉じる"></button>
        <div class="progress-bar" id="dialogspin">
            <div class="indeterminate"></div>
        </div>
        <iframe src="about:blank" frameborder="0" name="formsend"
            sandbox="allow-scripts"></iframe>
    </dialog>
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
                    <span id="post-<?= $citem["id"] ?>"><?= $citem["name"] ?>:
                        <?= htmlspecialchars($citem["text"]) ?> - <span
                            id="date-<?= $citem["unixtime"] ?>"><?= date("Y/m/d H:i:s", $citem["unixtime"]) . " UTC" ?></span></span>
                    <script>
                        document.getElementById("date-<?= $citem["unixtime"] ?>").innerText = date2str(new Date(<?= $citem["unixtime"] * 1000 ?>))
                    </script>
                    <form action="remove.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $citem["id"] ?>">
                        <input type="submit" value="削除">
                    </form>
                    <?php
                    generateHTML($citem["id"])
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
    generateHTML("root")
        ?>
</body>

</html>