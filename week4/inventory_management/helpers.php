<?php
// カテゴリの正解リスト。フォームのselect生成・フィルタのselect生成・保存時の検証、すべてここを見る
const CATEGORIES = ['文房具', '家電', '食品', '書籍', '雑貨'];

// ORDER BY はプリペアドステートメントでバインドできないため、
// 許可したカラムだけを通すホワイトリストで代用する
// （キー = URLのsort値 / 値 = 実際のカラム名。この2つを分けておくと、
//   将来カラム名を変えてもURLの互換を保てる）
const ALLOWED_SORTS = [
    'stock'      => 'stock',
    'price'      => 'price',
    'name'       => 'name',
    'created_at' => 'created_at',
];

// 現在のGETパラメータを保ったまま一部だけ差し替えたURLを作る
// 例: buildUrl(['sort' => 'price']) → 今のcategory等を維持したまま sort だけ差し替え
function buildUrl(array $overrides) {
    $params = array_merge($_GET, $overrides);
    return '?' . http_build_query($params);
}
