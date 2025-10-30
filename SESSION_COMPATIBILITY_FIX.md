# CakePHP 2とCakePHP 5のセッション互換性修正

## 問題

CakePHP 2で作成したセッションをCakePHP 5で読み取ろうとすると、内部的にエラーが発生し、新しいセッションが発行されていました。

## 根本原因

CakePHP 2とCakePHP 5では、セッションデータのシリアライゼーション形式が異なります：

- **CakePHP 2**: データを二重にシリアライズ（`serialize(session_encode($data))`の形式）
  ```
  s:168:"Config|a:3:{s:9:\"userAgent\";...}visit_count|i:1;";
  ```

- **CakePHP 5**: 標準のPHPセッション形式（`session_encode($data)`の形式）
  ```
  Config|a:3:{s:9:"userAgent";...}visit_count|i:1;
  ```

## 解決方法

CakePHP 5側にカスタムセッションハンドラーを作成し、CakePHP 2の形式を読み取れるようにしました。

### 1. カスタムセッションハンドラーの作成

ファイル: `cake5/src/Http/Session/Cake2CompatibleCacheSession.php`

このハンドラーは以下の機能を提供します：
- **read()**: CakePHP 2の二重シリアライズされたデータを`unserialize()`で展開
- **write()**: CakePHP 2互換形式で保存（`serialize()`で包む）

### 2. CakePHP 5の設定変更

ファイル: `cake5/config/app_local.php`

```php
'Session' => [
    'defaults' => 'php',
    'handler' => [
        'engine' => 'App\Http\Session\Cake2CompatibleCacheSession',
        'config' => 'redis_session',
    ],
    'cookie' => 'CAKEPHP',
    'timeout' => 120,
    'cookiePath' => '/',
],
```

### 3. CakePHP 2の設定変更

ファイル: `cake2/app/Config/core.php`

明示的にcookie名とpathを設定：

```php
Configure::write('Session', array(
    'defaults' => 'php',
    'handler' => array(
        'engine' => 'CacheSession',
        'config' => 'redis_session'
    ),
    'cookie' => 'CAKEPHP',
    'cookiePath' => '/',
    'timeout' => 120,
    'checkAgent' => false
));
```

## テスト手順

### 1. CakePHP 2でセッションを作成

```bash
# ブラウザで以下にアクセス
http://localhost:8080/session_test

# セッションIDをメモ（ページに表示されます）
# 例: 630c5a47a3a6890239c64d9906440526
```

### 2. 同じブラウザでCakePHP 5にアクセス

```bash
# 同じブラウザで以下にアクセス（同じセッションクッキーを使用）
http://localhost:8081/session-test

# visit_countがCakePHP 2で設定した値から継続されていることを確認
```

### 3. Redisで直接確認

```bash
# Redisに接続
docker-compose exec redis redis-cli

# セッションデータを確認
GET cake_session_<session_id>

# 出力例（CakePHP 2形式）:
# "s:168:\"Config|a:3:{...}visit_count|i:2;last_visit|s:19:\"2025-10-29 10:00:00\";\""
```

## 注意事項

### セッションデータの互換性

- **Config.userAgent**: CakePHP 2が自動的に保存する。CakePHP 5では無視される
- **Config.time**: CakePHP 2のタイムアウト管理用
- **Config.countdown**: CakePHP 2のセッション再生成用

これらのCakePHP 2固有のフィールドはCakePHP 5では使用されませんが、問題は発生しません。

### データ型の互換性

両バージョンともPHPのシリアライゼーションを使用しているため、以下のデータ型は正常に共有できます：

- 文字列（string）
- 整数（integer）
- 浮動小数点数（float）
- 配列（array）
- ブール値（boolean）
- null

オブジェクトの共有は避けるべきです。

## トラブルシューティング

### セッションが共有されない場合

1. **Cookie名が一致しているか確認**
   - 両方の設定で `'cookie' => 'CAKEPHP'` が設定されているか

2. **CookiePathが一致しているか確認**
   - 両方の設定で `'cookiePath' => '/'` が設定されているか

3. **Redisプレフィックスが一致しているか確認**
   - 両方で `'prefix' => 'cake_session_'` が設定されているか

4. **コンテナを再起動**
   ```bash
   docker-compose restart web cake5
   ```

### エラーログの確認

```bash
# CakePHP 5のエラーログ
docker-compose exec cake5 tail -f /var/www/html/logs/error.log

# CakePHP 2のエラーログ
docker-compose exec web tail -f /var/www/html/app/tmp/logs/error.log
```

## 実装の詳細

### Cake2CompatibleCacheSessionの仕組み

```php
// read時
public function read($id): string|false
{
    $data = Cache::read($id, $this->config);
    
    // CakePHP 2形式: serialize(session_data)
    $unserializedData = @unserialize($data);
    
    if ($unserializedData !== false && is_string($unserializedData)) {
        // 成功 - 内側のセッションデータを返す
        return $unserializedData;
    }
    
    // すでに標準形式の場合はそのまま返す
    return $data;
}

// write時
public function write($id, $data): bool
{
    // CakePHP 2互換形式で保存
    $wrappedData = serialize($data);
    return Cache::write($id, $wrappedData, $this->config);
}
```

この実装により：
- CakePHP 2が書き込んだデータをCakePHP 5が読み取れる
- CakePHP 5が書き込んだデータをCakePHP 2が読み取れる
- 双方向の互換性を実現
