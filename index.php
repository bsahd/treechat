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
    <style><?php include("style.css")?></style>
</head>

<body>
    <h1>ツリーチャット</h1>
    <p><?= $_SESSION["name"] ?> としてログインしています <a href="logout.php">ログアウト</a><a href="passwd.php">パスワード変更</a></p>
    <p id="updateStatus"><?= $nowtime ?>時点の情報です</p>
    <script>
        function date2str(d) {
            return d.toLocaleString("ja", {
                "hour12": false,
                "year": "numeric",
                "month": "2-digit",
                "day": "2-digit",
                "hour": "2-digit",
                "minute": "2-digit",
                "second": "2-digit",
                "timeZoneName": "short"
            });
        }
        var CHAT_HASH = "<?= hash("sha256", $treetext) ?>"
        var updateStatus = document.getElementById("updateStatus")
        updateStatus.innerText = date2str(new Date(<?= $_SERVER['REQUEST_TIME'] * 1000 ?>)) + "時点の情報です";
        async function checkUpdate() {
            try {
                var a = await (await fetch("chathash.php")).text()
                if (CHAT_HASH != a) {
                    updateStatus.innerText = "更新があります"
                    location.reload()
                } else {
                    var v = date2str(new Date());
                    updateStatus.innerText = "✅" + v + ": 更新なし"
                    setTimeout(checkUpdate, 500);
                }
            }catch{
                updateStatus.innerText = "⛔エラー"
            }
        }
        setTimeout(checkUpdate, 500);
    </script>
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
                    <span id="post-<?= $citem["id"] ?>"><?= $citem["name"] ?>: <?= htmlspecialchars($citem["text"]) ?> - <span id="date-<?= $citem["unixtime"] ?>"><?= date("Y/m/d H:i:s", $citem["unixtime"]) . " UTC" ?></span></span>
                    <script>
                        document.getElementById("date-<?= $citem["unixtime"] ?>").innerText = date2str(new Date(<?= $citem["unixtime"] * 1000 ?>))
                    </script>
                    <?php
                    if ($citem["name"] == $_SESSION["name"]) {
                    ?>
                        <form action="remove.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $citem["id"] ?>">
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
                    <label>返信:<input type="text" name="text" size="40" autocomplete="off"></label>
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
