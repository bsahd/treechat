<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ツリーチャット:ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>ツリーチャット:ログイン</h1>
    <form action="f_login.php" method="post">
        <div>
            <label>
                ユーザー名
                <input type="text" name="name" required autocomplete="off">
            </label>
        </div>
        <div>
            <label>
                パスワード
                <input type="password" name="pass" autocomplete="off">
            </label>
        </div>
        <input type="submit" value="ログイン">
    </form>

</body>

</html>