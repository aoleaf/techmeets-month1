# Week 4 基本課題 — ユーザー管理CRUDアプリ

PHP + MySQL によるユーザー管理システム。一覧・登録・編集・削除の4機能を実装。

## 実装状況

### 必須機能

| 要件 | 状況 | 実装箇所 |
|---|---|---|
| 全ユーザーをテーブル形式で表示（登録日の新しい順） | ✅ | `index.php`（`ORDER BY created_at DESC`） |
| ユーザー名・メール・年齢を入力できる | ✅ | `create.php` |
| 未入力の場合はエラーメッセージを表示する | ✅ | `create.php`（3項目すべて必須チェック） |
| 既存データをフォームに表示する | ✅ | `edit.php`（GETのidでSELECTし value に反映） |
| 変更内容を保存する | ✅ | `edit.php`（UPDATE） |
| 削除確認ダイアログを表示する | ✅ | `index.php`（削除リンクの `onclick="return confirm(...)"`） |

### セキュリティ要件

| 要件 | 状況 | 備考 |
|---|---|---|
| 全てのSQL文でプリペアドステートメント | ✅ | INSERT / SELECT / UPDATE / DELETE の5箇所すべて `prepare()` + `bind_param()`。外部入力のない一覧のSELECTも含め例外なく適用 |
| 表示時に `htmlspecialchars()` でXSS対策 | ✅ | 一覧の全カラム・リンクのid、フォームの value、エラーメッセージすべてに適用 |

### 入力バリデーション（補足対応）

- ユーザー名・メール・年齢が未入力の場合はエラー表示（`create.php` / `edit.php` 共通）
- 年齢は `ctype_digit()` で数値チェック。空文字が `bind_param("i")` で 0 に変換されて登録される問題を修正済み

## ファイル構成

```
month1/week4/
├── docker-compose.yml   # MySQL 8.0 + phpMyAdmin
├── db_test.php          # DB接続確認用
├── user_management/
│   ├── db.php           # DB接続処理（getDBConnection()）
│   ├── index.php        # ユーザー一覧（Read）
│   ├── create.php       # ユーザー登録（Create）
│   ├── edit.php         # ユーザー編集（Update）
│   ├── delete.php       # ユーザー削除（Delete）
│   └── style.css        # スタイル
└── README.md
```

## 起動方法

DBコンテナを起動:

```bash
docker compose up -d
```

- MySQL: `127.0.0.1:3306`（DB名 `myapp_db` / user `root` / pass `root`）
- phpMyAdmin: http://localhost:8080

PHPはビルトインサーバーで起動:

```bash
cd user_management
php -S localhost:8000
```

http://localhost:8000 にアクセス。

## usersテーブル

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 今後の課題

- `age` カラムが NULL を許可しているため、既存のNULLデータに対して `htmlspecialchars(null)` がPHP 8.1以降で非推奨警告を出す。`NOT NULL` 化を検討
- メールアドレスの形式チェックがサーバー側にない（現状はHTMLの `type="email"` のみ）
- 削除がGETリクエストのため、POST + CSRFトークン化が望ましい
