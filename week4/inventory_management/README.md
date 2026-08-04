# Week 4 実践課題 — 在庫管理CRUDアプリ

PHP + MySQL による在庫管理システム。一覧・登録・編集・削除に加え、**カテゴリ絞り込み**と**列ごとの並び替え**を実装。

## 実装状況

### 必須機能

| 要件 | 状況 | 実装箇所 |
|---|---|---|
| 全商品をテーブル形式で表示 | ✅ | `index.php` |
| 商品名・価格・在庫数・カテゴリを入力できる | ✅ | `create.php` |
| 未入力の場合はエラーメッセージを表示する | ✅ | `create.php`（4項目すべて必須チェック） |
| 既存データをフォームに表示する | ✅ | `edit.php`（GETのidでSELECTし value / selected に反映） |
| 変更内容を保存する | ✅ | `edit.php`（UPDATE） |
| 削除確認ダイアログを表示する | ✅ | `index.php`（削除リンクの `onclick="return confirm(...)"`） |

### 発展機能

| 要件 | 状況 | 実装箇所 |
|---|---|---|
| カテゴリで絞り込み | ✅ | `index.php`（`<form method="GET">` + `WHERE category = ?`） |
| 各列で並び替え（昇順/降順トグル） | ✅ | `index.php`（列ヘッダのリンク。再クリックで昇降反転） |
| 絞り込みと並び替えの同時保持 | ✅ | 並び替えリンクは `buildUrl()` でcategoryを維持、絞り込みフォームはsort/orderをhiddenで持ち回り |

### セキュリティ要件

| 要件 | 状況 | 備考 |
|---|---|---|
| 全てのSQL文でプリペアドステートメント | ✅ | INSERT / SELECT / UPDATE / DELETE すべて `prepare()` + `bind_param()` |
| **`ORDER BY` はバインド不可のためホワイトリスト方式で対応** | ✅ | 👇 下記参照。**今回いちばんの見せどころ** |
| 表示時に `htmlspecialchars()` でXSS対策 | ✅ | 一覧の全カラム・リンクのhref・フォームのvalue・エラーメッセージすべてに適用 |
| カテゴリのサーバー側検証 | ✅ | `<select>` でもブラウザ外から任意値を送れるため `in_array($category, CATEGORIES, true)` で照合 |

#### ORDER BY のホワイトリスト方式（重要）

プリペアドステートメントの `?` はテーブル名・カラム名・`ASC`/`DESC` にはバインドできない。
そのため、ユーザー入力を直接 `ORDER BY` に埋め込むとSQLインジェクションの穴になる。

対策として `helpers.php` の `ALLOWED_SORTS`（許可カラムのホワイトリスト）を用意し、
URLの `sort` パラメータが**このキーに一致したときだけ**対応するカラム名を採用する。
一致しなければ `created_at` にフォールバックする。

```php
const ALLOWED_SORTS = [
    'stock' => 'stock', 'price' => 'price',
    'name'  => 'name',  'created_at' => 'created_at',
];
$sortKey    = array_key_exists($_GET['sort'] ?? '', ALLOWED_SORTS) ? $_GET['sort'] : 'created_at';
$sortColumn = ALLOWED_SORTS[$sortKey];               // ← 必ず定義済みの安全な文字列
$order      = (($_GET['order'] ?? '') === 'asc') ? 'ASC' : 'DESC';  // ← asc一致以外はDESCに潰す
```

こうして `ORDER BY $sortColumn $order` に入る値は必ずコード内で定義した固定文字列に限定され、
外部からの任意入力がSQLに混入しない。

## 入力バリデーション

- 商品名・価格・在庫数・カテゴリが未入力ならエラー表示（`create.php` / `edit.php` 共通）
- 価格は `is_numeric()` かつ 0以上
- 在庫数は `ctype_digit()`（空文字が `bind_param("i")` で 0 に化けるのを防ぐ。week4のageと同じ理由）
- カテゴリは `CATEGORIES` との照合
- `bind_param("sdis", ...)` — string / double / integer / string の順。型と順番の対応に注意

## ファイル構成

```
month1/week4/inventory_management/
├── db.php          # DB接続処理（getDBConnection()）※user_managementと共通
├── helpers.php     # CATEGORIES / ALLOWED_SORTS / buildUrl()
├── schema.sql      # productsテーブル定義 + サンプルデータ10件
├── index.php       # 商品一覧（Read）+ 絞り込み + 並び替え
├── create.php      # 商品登録（Create）
├── edit.php        # 商品編集（Update）
├── delete.php      # 商品削除（Delete）
└── style.css       # スタイル
```

## テーブル定義

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,   -- 合計10桁・小数点以下2桁（例: 9999999.99）
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

サンプルデータは `schema.sql` に10件（カテゴリ・在庫数をバラけさせ、在庫0を1件含む）。

## 起動方法

week4の `docker-compose.yml`（MySQL 8.0 + phpMyAdmin）をそのまま利用する。

DBコンテナを起動:

```bash
cd month1/week4
docker compose up -d
```

- MySQL: `127.0.0.1:3306`（DB名 `myapp_db` / user `root` / pass `root`）
- phpMyAdmin: http://localhost:8080

テーブル作成とサンプル投入（`schema.sql` を実行）:

```bash
docker exec -i week4_db mysql -uroot -proot --default-character-set=utf8mb4 myapp_db < inventory_management/schema.sql
```

> ⚠️ `--default-character-set=utf8mb4` は必須。付けないとmysqlクライアントが `latin1` 接続になり、
> 日本語のサンプルデータが二重エンコードで文字化けする（PHP側は `utf8mb4` で読むため画面で化ける）。
> `schema.sql` は先頭で `DROP TABLE IF EXISTS` するので、何度でも流し直せる。

PHPはビルトインサーバーで起動:

```bash
cd inventory_management
php -S localhost:8000
```

http://localhost:8000 にアクセス。

## 今後の課題

- 削除がGETリクエストのため、POST + CSRFトークン化が望ましい（week4から引き続きの課題）
- 編集/削除の遷移後に絞り込み条件が一覧に戻らない（編集リンクにsort/order/categoryを載せ、更新後もその条件へ戻す拡張が可能）
- 在庫数のしきい値（例: 5未満）を色分け表示するなどの在庫アラート
- 価格・在庫のキーワード/範囲検索
