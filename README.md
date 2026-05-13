# Month1 - TechMeets 学習成果物

## 概要

TechMeets Month1 の学習成果物です。HTML/CSS/JavaScript のみを使って、Week1・Week2 にわたって複数の Web アプリを実装しています。

## フォルダ構成

```
month1/
├── week1/
│   └── index.html              # ToDoリスト（基本版）
└── week2/
    ├── index.html              # 積読管理ページ
    ├── experiment-1/
    │   └── index.html          # スライドショー
    ├── experiment-2/
    │   └── index.html          # プロフィールカード
    └── experiment-3/
        └── index.html          # ToDoリスト（拡張版）
```

## 各アプリの説明

### Week1 — ToDoリスト（基本版）

`week1/index.html`

- タスクの追加（テキスト入力 → 「追加」ボタン または Enter キー）
- タスクの完了切替（タスク名クリック）
- タスクの削除
- 完了件数の表示
- ダーク / ライトモード切替（設定は localStorage に保存）

---

### Week2 — 積読管理ページ

`week2/index.html`

読みたい本・読んでいる本をカード形式で一覧管理するページ。

- 本のカードグリッド表示
- 読了状況バッジ（未読 / 読書中 / 読了）
- ジャンル・優先度・一言メモの表示

---

### Week2 — experiment-1：スライドショー

`week2/experiment-1/index.html`

画像を切り替えるシンプルなスライドショー。

---

### Week2 — experiment-2：プロフィールカード

`week2/experiment-2/index.html`

アバター・名前・自己紹介を表示するプロフィールカード UI。

---

### Week2 — experiment-3：ToDoリスト（拡張版）

`week2/experiment-3/index.html`

Week1 の ToDoリストに機能を追加した発展版。

- Week1 の全機能を継承
- タスクを localStorage に保存（ページ再読込後も保持）
- 締め切り日の設定（日付入力フィールド）
- 締め切りを過ぎたタスクを赤くハイライト
- フィルター機能（すべて / 未完了のみ / 完了済みのみ）
- キーワード検索による絞り込み

## 使い方

各 `index.html` をブラウザで開くだけで動作します。サーバー不要です。

## 技術スタック

- HTML5
- CSS3（CSS変数、Flexbox）
- JavaScript（Vanilla JS、localStorage）
