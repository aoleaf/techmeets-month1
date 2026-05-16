<?php
$errors = [];
$submitted = false;

$name    = '';
$email   = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

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
        $submitted = true;
    }
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
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

    /* ---- form ---- */
    .form-group {
      margin-bottom: 1.25rem;
    }

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
      transition: background .2s;
    }

    .btn:hover { background: #3182ce; }

    /* ---- confirm ---- */
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

    .badge {
      display: inline-block;
      background: #ebf8ff;
      color: #2b6cb0;
      border-radius: 6px;
      padding: .15rem .6rem;
      font-size: .75rem;
      font-weight: 700;
      margin-bottom: 1rem;
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

<?php if ($submitted): ?>

  <!-- 確認画面 -->
  <div class="badge">送信内容の確認</div>
  <h1>お問い合わせを受け付けました</h1>

  <table class="confirm-table">
    <tr>
      <th>お名前</th>
      <td><?= h($name) ?></td>
    </tr>
    <tr>
      <th>メールアドレス</th>
      <td><?= h($email) ?></td>
    </tr>
    <tr>
      <th>件名</th>
      <td><?= h($subject) ?></td>
    </tr>
    <tr>
      <th>メッセージ</th>
      <td><?= h($message) ?></td>
    </tr>
  </table>

  <a class="back-link" href="contact.php">← フォームに戻る</a>

<?php else: ?>

  <!-- 入力フォーム -->
  <h1>お問い合わせ</h1>

  <form method="POST" action="contact.php" novalidate>

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

    <button type="submit" class="btn">送信する</button>

  </form>

<?php endif; ?>

</div>
</body>
</html>
