<?php
require_once 'db.php';
require_once 'helpers.php';

$error = '';

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');
    $category = $_POST['category'] ?? '';

    if ($name === '' || $price === '' || $stock === '' || $category === '') {
        $error = '商品名、価格、在庫数、カテゴリはすべて必須です。';
    } elseif (!is_numeric($price) || $price < 0) {
        $error = '価格は0以上の数値で入力してください。';
    } elseif (!ctype_digit($stock)) {
        $error = '在庫数は0以上の整数で入力してください。';
    } elseif (!in_array($category, CATEGORIES, true)) {
        // <select>でもブラウザ外から任意の値を送れるため、サーバー側でも正解リストと照合する
        $error = 'カテゴリの値が不正です。';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)");
        // "sdis" = name(string) / price(double) / stock(integer) / category(string)。順番と型の対応に注意
        $stmt->bind_param("sdis", $name, $price, $stock, $category);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            // 登録成功したら一覧ページに移動する
            header('Location: index.php');
            exit;
        } else {
            $error = '登録に失敗しました: ' . $stmt->error;
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>商品登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規商品登録</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- 失敗時は入力値を value に戻す（カテゴリは selected を復元する） -->
    <form method="POST">
        <label>商品名:
            <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </label><br>
        <label>価格:
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
        </label><br>
        <label>在庫数:
            <input type="number" name="stock" value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>">
        </label><br>
        <label>カテゴリ:
            <select name="category">
                <option value="">選択してください</option>
                <?php foreach (CATEGORIES as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo ($_POST['category'] ?? '') === $cat ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>
