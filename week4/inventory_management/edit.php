<?php
require_once 'db.php';
require_once 'helpers.php';

$id    = $_GET['id'] ?? '';
$error = '';

$conn = getDBConnection();

// 編集対象の商品を取得（GETパラメータのid で検索）
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result  = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// 商品が存在しない場合は一覧に戻す
if (!$product) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// フォームが送信されたとき（POSTリクエスト）— バリデーションは create.php と同一
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
        $error = 'カテゴリの値が不正です。';
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, category = ? WHERE id = ?");
        // "sdisi" = name / price / stock / category / id(WHERE用)
        $stmt->bind_param("sdisi", $name, $price, $stock, $category, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        } else {
            $error = '更新に失敗しました: ' . $stmt->error;
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>商品編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>商品編集</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- value には「POSTで送られた値」か「DBから取得した現在の値」を表示する -->
    <form method="POST">
        <label>商品名:
            <input type="text" name="name"
                value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>">
        </label><br>
        <label>価格:
            <input type="number" step="0.01" name="price"
                value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>">
        </label><br>
        <label>在庫数:
            <input type="number" name="stock"
                value="<?php echo htmlspecialchars($_POST['stock'] ?? $product['stock']); ?>">
        </label><br>
        <label>カテゴリ:
            <select name="category">
                <?php foreach (CATEGORIES as $cat): ?>
                <?php // 現在のカテゴリ（POSTがあればそちら、無ければDBの値）に selected を付ける ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo ($_POST['category'] ?? $product['category']) === $cat ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>
