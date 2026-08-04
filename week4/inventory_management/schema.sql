-- 何度でも流し直せるように、既存テーブルがあれば作り直す
DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,  -- DECIMAL(10,2): 小数点を含む数値。合計10桁・小数点以下2桁（例: 9999999.99）
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- サンプルデータ（カテゴリ・在庫数をバラけさせて10件）
-- ・カテゴリごとの件数を不均一にして、フィルタの絞り込みが効いているか確認しやすくする
-- ・在庫0の商品を1件入れて、DEFAULT 0 と stock の並び替えの端（先頭/末尾）を確認できるようにする
INSERT INTO products (name, price, stock, category) VALUES
    ('三色ボールペン',      180.00,  120, '文房具'),
    ('A4ノート 5冊セット',  550.00,   40, '文房具'),
    ('油性マーカー',        130.00,    0, '文房具'),
    ('ワイヤレスイヤホン', 8980.00,   15, '家電'),
    ('USB充電器 30W',      2480.00,   60, '家電'),
    ('ドリップコーヒー',    980.00,   35, '食品'),
    ('紅茶ティーバッグ',    650.00,   80, '食品'),
    ('PHP実践入門',        3200.00,    8, '書籍'),
    ('データベース設計',    3960.00,    5, '書籍'),
    ('マグカップ',          1200.00,   25, '雑貨');
