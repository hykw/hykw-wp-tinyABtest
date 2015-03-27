# hykw-wp-tinyABtest
簡易的なABテスト支援プラグイン

# 使い方

自分が使うときは global 変数としてインスタンスを作っちゃって、各所で global なり $GLOBALS なりで呼び出しています。


## インスタンスを作成
functions.php あたりで。

```php
$gobjABTest = new NumericHYKWTinyABTest();   // cookieの値が数値のみの場合
```

## ヘッダ出力前に Cookie を出力
headers.php あたりで。

```php
$GLOBALS['gobjABTest']->writeABCookie();
```

## ABテストを行う箇所で
```php
$dice = $GLOBALS['gobjABTest']->getDice();
if ($dice == 0)
  // Aパターン
else
  // Bパターン
      ・
      ・
      ・

```

