<?php $nowtime = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']) . " UTC";
session_start(['read_and_close' => 1]);
$noform = isset($_GET["form"]) ? (bool) $_GET["form"] : false;
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
    <script src="index.js" defer></script>
    <script src="htmx.js" defer></script>
</head>

<body hx-boost="true" hx-indicator="#loading">
    <header>
        <span>
            <form hx-get="./" hx-trigger="change from:select" hx-target="body"
                hx-push-url="true" style="display: inline;">
                <select name="form">
                    <option value="" <?= $noform ? "" : "selected" ?>>書き込み
                    </option>
                    <option value="true" <?= $noform ? "selected" : "" ?>>閲覧
                    </option>
                </select>
                <button id="manual-submit">切り替え</button>
            </form>
        </span>
        <span><span
                hx-get="./checkupdate.php?hash=<?= hash("sha256", $treetext) ?>"
                hx-trigger="every 2s" hx-indicator="#updateloading"><span
                    id="updateloading">⌛️</span><span
                    class="checkmark">⌚</span><?= $nowtime ?>時点の情報です</span>
            : <a href="./?<?= $_SERVER["QUERY_STRING"] ?>">再読み込み</a></span>
        </span>
        <span><?= $_SESSION["name"] ?> <a href="logout.php">ログアウト</a> <a
                href="passwd.php">パスワード変更</a></span>
    </header>
    <div id="loading">
        <div class="progress-bar" id="dialogspin">
            <div class="indeterminate"></div>
        </div>
    </div>
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