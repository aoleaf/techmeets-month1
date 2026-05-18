<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>コメント投稿</title>
</head>
<body>

<h1>コメント投稿フォーム</h1>

<form method="POST">
  <label>名前:</label>
  <input type="text" name="name"><br>
  <label>コメント:</label>
  <textarea name="comment"></textarea><br>
  <button type="submit">投稿する</button>
</form>

<?php
// フォームが送信されたときの処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST["name"];
    $comment = $_POST["comment"];

    // nameとcommentの両方が入力されているかを確認し、入力されていない場合はエラーメッセージを表示
    if ($name == "") {
        echo "名前を入力してください。";
    } elseif ($comment == "") {
        echo "コメントを入力してください。";
    } 
    //  両方とも入力されている場合は、名前とコメントを安全に表示
     else {
        echo "<p>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "さんのコメント:</p>";
        echo "<p>" . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . "</p>";
    }
}
?>

</body>
</html>