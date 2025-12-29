<?php
session_start(['read_and_close' => 1]);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ツリーチャット:パスワード変更</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>ツリーチャット:パスワード変更</h1>
    <p><?= $_SESSION["name"] ?> としてログインしています
    <form style="display: inline;" action="./logout.php" method="POST">
        <button>ログアウト</button>
    </form>
    </p>

    <form action="f_passwd.php" method="post">
        <div>
            <label>
                パスワード
                <input type="password" name="pass" required autocomplete="new-password">
            </label>
            <label>
                再入力
                <input type="password" name="pass2" required autocomplete="new-password">
            </label>
        </div>
        <input type="submit" value="変更">
    </form>
    <a href="./">キャンセル</a>

</body>

</html>