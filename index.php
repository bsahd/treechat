<?php $nowtime = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']) . " UTC";
session_start(['read_and_close' => 1]);
$noform = isset($_GET["viewmode"]);
$fp = fopen("chat.json", "r");
if (flock($fp, LOCK_SH)) { // 排他ロックを確保します
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
</head>

<body>
    <header>
        <span>
            <form style="display: inline;" action="./" method="GET"
                id="viewmodeform">
                <label><input type="checkbox" name="viewmode" value="true"
                        <?= $noform ? "checked" : "" ?>>表示モード</label>
                <button>送信</button>
                <script>
                    document.getElementsByName("viewmode")[0].addEventListener("change", (e) => {
                        document.getElementById("viewmodeform").submit()
                    })
                </script>
            </form>
        </span>
        <span id="updatecheck">⌚<?= $nowtime ?>時点の情報です</span>
        <script>
            setInterval(async () => {
                document.getElementById("updatecheck").innerHTML = await (await fetch("./checkupdate.php?hash=<?= hash("sha256", $treetext) ?>")).text();
                setupDateFormatting()
            }, 2000)
        </script>
        </span>
        <span><?= $_SESSION["name"] ?>
            <form style="display: inline;" action="./logout.php" method="POST">
                <button>ログアウト</button>
            </form>
            <a href="passwd.php">パスワード変更</a>
        </span>
    </header>
    <h1>ツリーチャット</h1>

    <?php
    function createChatTree($root)
    {
        ?>
        <ul>
            <?php
            global $tree;
            global $noform;
            $children = array_filter($tree, function ($item) use ($root) {
                return $item["parent"] == $root;
            });
            foreach ($children as $citem) {
                ?>
                <li>
                    <span id="post-<?= $citem["id"] ?>"><?= $citem["name"] ?>:
                        <?= htmlspecialchars($citem["text"]) ?> - <span
                            data-unixtime="<?= $citem["unixtime"] ?>"><?= date("Y/m/d H:i:s", $citem["unixtime"]) . " UTC" ?></span></span>
                    <?php if (!$noform) { ?>
                        <form action="remove.php" method="post" style="display:inline;"
                            hx-boost="true">
                            <input type="hidden" name="id" value="<?= $citem["id"] ?>">
                            <input type="submit" value="削除">
                        </form>
                        <?php
                    }
                    createChatTree($citem["id"])
                        ?>
                </li>
                <?php
            }
            if (!$noform) {
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
            <?php } ?>
        </ul>
        <?php
    }
    createChatTree("root")
        ?>
</body>

</html>