<?php
$fp = fopen("chat.json", "r");
if (flock($fp, LOCK_SH)) {  // 排他ロックを確保します
  $treetext = fread($fp, filesize("chat.json"));
} else {
  echo "ファイルを取得できません!";
  exit;
}
fclose($fp);
$ctime = time();
$hash = hash("sha256", $treetext);
if ($_GET["hash"] != $hash) {
  ?>
  🔔更新があります
  <?php
} else {
  ?>
  <span id="updateloading">⌛️</span><span class="checkmark">✅</span><span
    data-unixtime="<?= $ctime ?>"><?= date("Y/m/d H:i:s", $ctime) . " UTC" ?></span>:
  更新なし
  <?php
}