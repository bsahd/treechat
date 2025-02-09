<?php
session_start(['read_and_close'=>1]);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ツリーチャット:パスワード変更</title>
    <style><?php include("style.css")?></style>
</head>

<body>
    <h1>ツリーチャット:パスワード変更</h1>
    <p><?= $_SESSION["name"]?> としてログインしています <a href="logout.php">ログアウト</a></p>

    <form action="f_passwd.php" method="post">
        <div>
            <label>
                パスワード
                <input type="password" name="pass" required autocomplete="off">
            </label>
            <label>
                再入力
                <input type="password" name="pass2" required autocomplete="off">
            </label>
        </div>
        <input type="submit" value="変更">
    </form>

</body>

</html>