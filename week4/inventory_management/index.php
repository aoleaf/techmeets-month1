<?php
require_once 'db.php';
require_once 'helpers.php';

// --- カテゴリ（絞り込み） ---
// リストにない値・空文字なら「すべて」扱いにフォールバックする
$category = $_GET['category'] ?? '';
if (!in_array($category, CATEGORIES, true)) {
    $category = '';
}

// --- 並び替えのカラム ---
// ORDER BY はバインドできないので、ALLOWED_SORTS のキーに一致する場合だけ採用。
// 無ければ created_at（登録日）にフォールバック
$sortKey = $_GET['sort'] ?? 'created_at';
if (!array_key_exists($sortKey, ALLOWED_SORTS)) {
    $sortKey = 'created_at';
}
$sortColumn = ALLOWED_SORTS[$sortKey];

// --- 並び順 ---
// 'asc' と完全一致したときだけ昇順。それ以外は降順に潰す（任意の値を弾く）
$order = (($_GET['order'] ?? '') === 'asc') ? 'ASC' : 'DESC';

// --- 取得 ---
// カテゴリ指定の有無で prepare を2分岐（指定時のみ bind_param する）。
// $sortColumn と $order はホワイトリストを通した安全な値なので、そのまま埋め込む
$conn = getDBConnection();
if ($category === '') {
    $sql  = "SELECT id, name, price, stock, category, created_at FROM products ORDER BY $sortColumn $order";
    $stmt = $conn->prepare($sql);
} else {
    $sql  = "SELECT id, name, price, stock, category, created_at FROM products WHERE category = ? ORDER BY $sortColumn $order";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}
$stmt->execute();
$result = $stmt->get_result();

// 列ヘッダの並び替えリンクを生成する。
// ・現在の絞り込み（category等）を維持するため buildUrl() を使う
// ・同じ列を再クリックしたら昇順/降順が反転するように次のorderを決める
// ・現在ソート中の列には矢印（▲昇順 / ▼降順）を付ける
function sortHeader($label, $key) {
    global $sortKey, $order;
    $nextOrder = ($sortKey === $key && $order === 'ASC') ? 'desc' : 'asc';
    $arrow = '';
    if ($sortKey === $key) {
        $arrow = $order === 'ASC' ? ' ▲' : ' ▼';
    }
    $url = buildUrl(['sort' => $key, 'order' => $nextOrder]);
    return '<a href="' . htmlspecialchars($url) . '">'
         . htmlspecialchars($label . $arrow) . '</a>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>在庫管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>商品一覧</h1>
    <a href="create.php">新規商品登録</a>

    <!-- カテゴリ絞り込みフォーム。method="GET" でURLのクエリとして送る -->
    <!-- 送信時に現在の sort / order を hidden で持ち回り、絞り込みを変えても並び順が消えないようにする -->
    <form method="GET">
        <label>カテゴリ:
            <select name="category" onchange="this.form.submit()">
                <option value="">すべて</option>
                <?php foreach (CATEGORIES as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo $cat === $category ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="hidden" name="sort"  value="<?php echo htmlspecialchars($sortKey); ?>">
        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order === 'ASC' ? 'asc' : 'desc'); ?>">
        <button type="submit">絞り込む</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th><?php echo sortHeader('商品名', 'name'); ?></th>
                <th><?php echo sortHeader('価格', 'price'); ?></th>
                <th><?php echo sortHeader('在庫数', 'stock'); ?></th>
                <th>カテゴリ</th>
                <th><?php echo sortHeader('登録日', 'created_at'); ?></th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo htmlspecialchars($row['id']); ?>">編集</a>
                    <a href="delete.php?id=<?php echo htmlspecialchars($row['id']); ?>" onclick="return confirm('本当に削除しますか？')">削除</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
