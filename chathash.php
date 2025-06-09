<?php
$fp = fopen("chat.json", "r");
if (flock($fp, LOCK_SH)) {  // 排他ロックを確保します
    $treetext = fread($fp, filesize("chat.json"));
} else {
    echo "ファイルを取得できません!";
    exit;
}
fclose($fp);
?><?= hash("sha256", $treetext) ?>