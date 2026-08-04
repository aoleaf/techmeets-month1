<?php
require_once 'db.php';

$id = $_GET['id'] ?? '';

// idが指定されていない場合は一覧に戻す
if ($id === '') {
    header('Location: index.php');
    exit;
}

// week4のuser_managementをほぼそのまま流用（対象テーブルを products に変更）。
// 削除はGETのまま。POST + CSRFトークン化は今後の課題として残す（READMEに記載）
$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// 削除後は一覧ページに移動
header('Location: index.php');
exit;
?>
