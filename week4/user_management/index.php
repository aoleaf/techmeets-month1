<?php
require_once 'db.php';
$conn = getDBConnection();

// 全ユーザーを取得（外部入力はないが、SQLは一律プリペアドステートメントで実行する）
$stmt = $conn->prepare("SELECT id, username, email, age, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ユーザー管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー一覧</h1>
    <a href="create.php">新規ユーザー登録</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ユーザー名</th>
                <th>メール</th>
                <th>年齢</th>
                <th>登録日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
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