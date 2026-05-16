<?php
// HTMLエスケープ（XSS対策）
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 画面状態とエラー配列の初期化
// 画面遷移ステップ（input → confirm → complete）
$step   = $_POST['step'] ?? 'input';
$errors = [];

// フォーム入力値の初期化（POSTがあればその値を使用）
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($step === 'input') {
        // --- 入力 → 確認：必須チェックと形式チェックを行う ---
        if ($name === '') {
            $errors['name'] = '名前を入力してください。';
        }
        if ($email === '') {
            $errors['email'] = 'メールアドレスを入力してください。';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = '有効なメールアドレスを入力してください。';
        }
        if ($subject === '') {
            $errors['subject'] = '件名を入力してください。';
        }
        if ($message === '') {
            $errors['message'] = 'メッセージを入力してください。';
        }

        if (empty($errors)) {
            $step = 'confirm'; // 確認画面へ
        }
        // エラー時は $step を 'input' のまま維持

    } elseif ($step === 'confirm') {
        // --- 確認 → 完了：メール送信などの最終処理を行う ---
        $step = 'complete';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>お問い合わせ</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Hiragino Kaku Gothic ProN", "Noto Sans JP", sans-serif;
      background: #f4f6f9;
      color: #333;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,.08);
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 560px;
    }

    h1 {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #1a1a2e;
    }

    /* ステップ表示 */
    .steps {
      display: flex;
      gap: 0;
      margin-bottom: 2rem;
    }

    .step-item {
      flex: 1;
      text-align: center;
      font-size: .75rem;
      font-weight: 600;
      padding: .5rem .25rem;
      color: #a0aec0;
      border-bottom: 3px solid #e2e8f0;
    }

    .step-item.active {
      color: #4299e1;
      border-bottom-color: #4299e1;
    }

    .step-item.done {
      color: #48bb78;
      border-bottom-color: #48bb78;
    }

    /* フォーム */
    .form-group { margin-bottom: 1.25rem; }

    label {
      display: block;
      font-size: .875rem;
      font-weight: 600;
      margin-bottom: .35rem;
    }

    label .required {
      color: #e53e3e;
      margin-left: .25rem;
      font-size: .75rem;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: .65rem .85rem;
      border: 1.5px solid #cbd5e0;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: border-color .2s;
      background: #fafafa;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
      outline: none;
      border-color: #4299e1;
      background: #fff;
    }

    input.is-invalid,
    textarea.is-invalid {
      border-color: #e53e3e;
      background: #fff5f5;
    }

    textarea { resize: vertical; min-height: 140px; }

    .error-msg {
      color: #e53e3e;
      font-size: .8rem;
      margin-top: .3rem;
    }

    /* ボタン */
    .btn {
      display: inline-block;
      width: 100%;
      padding: .75rem;
      background: #4299e1;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background .2s, opacity .2s;
    }

    .btn:hover:not(:disabled) { background: #3182ce; }

    .btn:disabled {
      opacity: .55;
      cursor: not-allowed;
    }

    .btn-outline {
      background: #fff;
      color: #4299e1;
      border: 1.5px solid #4299e1;
      margin-top: .75rem;
    }

    .btn-outline:hover:not(:disabled) {
      background: #ebf8ff;
    }

    /* 確認テーブル */
    .confirm-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1.5rem;
    }

    .confirm-table th,
    .confirm-table td {
      text-align: left;
      padding: .75rem .5rem;
      border-bottom: 1px solid #e2e8f0;
      font-size: .95rem;
      vertical-align: top;
    }

    .confirm-table th {
      width: 30%;
      color: #718096;
      font-weight: 600;
    }

    .confirm-table td { white-space: pre-wrap; word-break: break-word; }

    /* 完了アイコン */
    .complete-icon {
      font-size: 3rem;
      text-align: center;
      margin-bottom: 1rem;
    }

    .complete-msg {
      text-align: center;
      color: #718096;
      font-size: .95rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: #4299e1;
      text-decoration: none;
      font-size: .9rem;
    }

    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="card">

  <!-- ステップインジケーター -->
  <div class="steps">
    <div class="step-item <?= $step === 'input'   ? 'active' : 'done' ?>">① 入力</div>
    <div class="step-item <?= $step === 'confirm'  ? 'active' : ($step === 'complete' ? 'done' : '') ?>">② 確認</div>
    <div class="step-item <?= $step === 'complete' ? 'active' : '' ?>">③ 完了</div>
  </div>

<?php if ($step === 'input'): ?>

  <!-- ① 入力フォーム -->
  <h1>お問い合わせ</h1>

  <form method="POST" action="contact.php" novalidate>
    <!-- 現在の画面ステップ（input）を次のPOSTに引き継ぐ -->
    <input type="hidden" name="step" value="input"> 

    <div class="form-group">
      <label for="name">お名前<span class="required">必須</span></label>
      <input
        type="text"
        id="name"
        name="name"
        value="<?= h($name) ?>"
        class="<?= isset($errors['name']) ? 'is-invalid' : '' ?>"
        autocomplete="name"
      >
      <?php if (isset($errors['name'])): ?>
        <p class="error-msg"><?= h($errors['name']) ?></p>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="email">メールアドレス<span class="required">必須</span></label>
      <input
        type="email"
        id="email"
        name="email"
        value="<?= h($email) ?>"
        class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>"
        autocomplete="email"
      >
      <?php if (isset($errors['email'])): ?>
        <p class="error-msg"><?= h($errors['email']) ?></p>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="subject">件名<span class="required">必須</span></label>
      <input
        type="text"
        id="subject"
        name="subject"
        value="<?= h($subject) ?>"
        class="<?= isset($errors['subject']) ? 'is-invalid' : '' ?>"
      >
      <?php if (isset($errors['subject'])): ?>
        <p class="error-msg"><?= h($errors['subject']) ?></p>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="message">メッセージ<span class="required">必須</span></label>
      <textarea
        id="message"
        name="message"
        class="<?= isset($errors['message']) ? 'is-invalid' : '' ?>"
      ><?= h($message) ?></textarea>
      <?php if (isset($errors['message'])): ?>
        <p class="error-msg"><?= h($errors['message']) ?></p>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn">確認画面へ</button>
  </form>

<?php elseif ($step === 'confirm'): ?>

  <!-- ② 確認画面 -->
  <h1>入力内容の確認</h1>

  <table class="confirm-table">
    <tr><th>お名前</th>        <td><?= h($name) ?></td></tr>
    <tr><th>メールアドレス</th> <td><?= h($email) ?></td></tr>
    <tr><th>件名</th>           <td><?= h($subject) ?></td></tr>
    <tr><th>メッセージ</th>     <td><?= h($message) ?></td></tr>
  </table>

  <!-- 入力内容を hidden で保持し、送信処理へ渡す -->
  <form method="POST" action="contact.php" id="confirmForm">
    <input type="hidden" name="step"    value="confirm">
    <input type="hidden" name="name"    value="<?= h($name) ?>">
    <input type="hidden" name="email"   value="<?= h($email) ?>">
    <input type="hidden" name="subject" value="<?= h($subject) ?>">
    <input type="hidden" name="message" value="<?= h($message) ?>">

    <button type="submit" class="btn" id="submitBtn">この内容で送信する</button>
  </form>

  <!-- 入力内容を保持したまま input 画面に戻る -->
  <form method="POST" action="contact.php">
    <input type="hidden" name="step"    value="input">
    <input type="hidden" name="name"    value="<?= h($name) ?>">
    <input type="hidden" name="email"   value="<?= h($email) ?>">
    <input type="hidden" name="subject" value="<?= h($subject) ?>">
    <input type="hidden" name="message" value="<?= h($message) ?>">
    <button type="submit" class="btn btn-outline">← 入力内容を修正する</button>
  </form>

  <script>
    document.getElementById('confirmForm').addEventListener('submit', function () {
      var btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.textContent = '送信中...';
    });
  </script>

<?php elseif ($step === 'complete'): ?>

  <!-- ③ 完了画面 -->
  <div class="complete-icon">&#10003;</div>
  <h1 style="text-align:center;">送信完了</h1>
  <p class="complete-msg">
    お問い合わせを受け付けました。<br>
    内容を確認のうえ、担当者よりご連絡いたします。
  </p>

  <table class="confirm-table">
    <tr><th>お名前</th>        <td><?= h($name) ?></td></tr>
    <tr><th>メールアドレス</th> <td><?= h($email) ?></td></tr>
    <tr><th>件名</th>           <td><?= h($subject) ?></td></tr>
    <tr><th>メッセージ</th>     <td><?= h($message) ?></td></tr>
  </table>

  <a class="back-link" href="contact.php">← トップに戻る</a>

<?php endif; ?>

</div>
</body>
</html>
