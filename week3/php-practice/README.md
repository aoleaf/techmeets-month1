# PHP Practice メモ

## comment.php のレビューで見つけた問題と修正

### 問題1：比較演算子のタイポ（`=` → `==`）

**修正前**
```php
if ($name = "") {
```

**修正後**
```php
if ($name == "") {
```

**問題点**  
`=` は代入演算子なので、条件式の中で使うと `$name` に空文字を代入してしまう。  
結果として条件は常に偽（空文字は falsy）になり、名前チェックが機能しない。

**学んだこと**  
比較には `==`（緩やかな比較）または `===`（厳密な比較）を使う。  
空文字チェックなら `=== ''` が最も明確。

---

### 問題2：出力時のエスケープ漏れ（XSS脆弱性）

**修正前**
```php
echo "<p>" . $name . "さんのコメント:</p>";
echo "<p>" . $comment . "</p>";
```

**修正後**
```php
echo "<p>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "さんのコメント:</p>";
echo "<p>" . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . "</p>";
```

**問題点**  
ユーザーが `<script>alert('XSS')</script>` などを入力した場合、そのままHTMLとして実行されてしまう（XSS攻撃）。

**学んだこと**  
ユーザー入力を画面に出力するときは必ず `htmlspecialchars()` でエスケープする。  
`ENT_QUOTES` を指定するとシングルクォートもエスケープされるため、属性値の中に出力する場合も安全になる。

---

### 問題3：`$comment` の空チェック漏れ

**修正前**
```php
if ($name = "") {
    echo "名前を入力してください。";
} else {
    echo "<p>" . $name . "さんのコメント:</p>";
    echo "<p>" . $comment . "</p>";
}
```

**修正後**
```php
if ($name == "") {
    echo "名前を入力してください。";
} elseif ($comment == "") {
    echo "コメントを入力してください。";
} else {
    echo "<p>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "さんのコメント:</p>";
    echo "<p>" . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . "</p>";
}
```

**問題点**  
`$name` の空チェックはあったが、`$comment` にはなかった。  
コメントが空のままでも投稿でき、空の `<p>` タグが出力されてしまう。

**学んだこと**  
必須項目はすべて空チェックを行う。  
バリデーション漏れは見落としやすいので、入力項目を一つずつ確認する習慣をつける。
