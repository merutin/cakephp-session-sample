# セッション共有について / Session Sharing

## 概要 / Overview

このプロジェクトでは、CakePHP 2とCakePHP 5が同じRedisインスタンスを使用してセッションを保存します。
両方のアプリケーションで `cake_session_` というプレフィックスを使用しているため、理論上はセッションデータを共有できます。

This project demonstrates how CakePHP 2 and CakePHP 5 can store sessions in the same Redis instance.
Both applications use the `cake_session_` prefix, allowing potential session data sharing.

## 設定詳細 / Configuration Details

### CakePHP 2 (ポート8080 / Port 8080)

**ファイル / File:** `cake2/app/Config/bootstrap.php`
```php
Cache::config('redis_session', array(
    'engine' => 'Redis',
    'prefix' => 'cake_session_',
    'server' => getenv('REDIS_HOST') ?: '127.0.0.1',
    'port' => getenv('REDIS_PORT') ?: 6379,
    'persistent' => false,
    'duration' => '+2 hours'
));
```

**ファイル / File:** `cake2/app/Config/core.php`
```php
Configure::write('Session', array(
    'defaults' => 'cache',
    'handler' => array(
        'config' => 'redis_session'
    )
));
```

### CakePHP 5 (ポート8081 / Port 8081)

**ファイル / File:** `cake5/config/app_local.php`
```php
'Cache' => [
    'redis_session' => [
        'className' => 'Cake\Cache\Engine\RedisEngine',
        'prefix' => 'cake_session_',
        'server' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'persistent' => false,
        'duration' => '+2 hours',
        'database' => 0,
    ],
],

'Session' => [
    'defaults' => 'cache',
    'handler' => [
        'config' => 'redis_session',
    ],
],
```

## セッションの確認方法 / How to Verify Sessions

### Redisに保存されたセッションを確認 / Check Sessions in Redis

```bash
# Redisコンテナに接続 / Connect to Redis container
docker-compose exec redis redis-cli

# セッションキーの一覧を表示 / List session keys
KEYS cake_session_*

# 特定のセッションデータを確認 / View specific session data
GET cake_session_<session_id>
```

### セッション動作テスト / Test Session Behavior

1. **CakePHP 2でセッションを作成 / Create session in CakePHP 2:**
   - http://localhost:8080/session_test にアクセス
   - ページをリロードして訪問回数を確認
   - セッションIDをメモ

2. **CakePHP 5でセッションを作成 / Create session in CakePHP 5:**
   - http://localhost:8081/session-test にアクセス
   - ページをリロードして訪問回数を確認
   - セッションIDをメモ

3. **Redisでセッションデータを確認 / Verify session data in Redis:**
   ```bash
   docker-compose exec redis redis-cli
   KEYS cake_session_*
   ```

## 技術的な違い / Technical Differences

### セッションストレージの実装 / Session Storage Implementation

| Feature | CakePHP 2 | CakePHP 5 |
|---------|-----------|-----------|
| Redisクライアント / Redis Client | phpredis extension | phpredis extension |
| キャッシュエンジン / Cache Engine | Redis | RedisEngine |
| セッションプレフィックス / Session Prefix | cake_session_ | cake_session_ |
| セッションハンドラー / Session Handler | Cache handler | Cache handler |
| PHPバージョン / PHP Version | 7.4 | 8.2 |

### セッションデータ形式 / Session Data Format

両バージョンとも、PHPのネイティブセッションシリアライゼーションを使用します。
Both versions use PHP's native session serialization.

- CakePHP 2: PHP serialize format
- CakePHP 5: PHP serialize format

## 互換性に関する注意事項 / Compatibility Notes

⚠️ **重要 / Important:**

セッションデータのフォーマットやシリアライゼーション方式が異なる可能性があるため、
完全なセッション共有には追加の実装が必要になる場合があります。

Due to potential differences in session data format and serialization methods,
full session sharing may require additional implementation.

このプロジェトは、両バージョンが同じRedisインスタンスを使用できることを示すものであり、
セッションデータの完全な互換性を保証するものではありません。

This project demonstrates that both versions can use the same Redis instance,
but does not guarantee full session data compatibility.

## トラブルシューティング / Troubleshooting

### セッションが共有されない場合 / If Sessions Are Not Shared

1. **プレフィックスの確認 / Check prefix:**
   両方の設定ファイルで `cake_session_` が使用されているか確認
   Verify that `cake_session_` is used in both configuration files

2. **Redisデータベース番号の確認 / Check Redis database number:**
   両方とも同じデータベース番号（デフォルトは0）を使用しているか確認
   Verify both use the same database number (default is 0)

3. **セッションIDの形式 / Session ID format:**
   ブラウザのクッキーでセッションIDを確認
   Check session IDs in browser cookies

4. **シリアライゼーション形式 / Serialization format:**
   PHPのセッションシリアライゼーション設定を確認
   Check PHP session serialization settings

## 参考資料 / References

- [CakePHP 2 Sessions Documentation](https://book.cakephp.org/2/en/development/sessions.html)
- [CakePHP 5 Sessions Documentation](https://book.cakephp.org/5/en/development/sessions.html)
- [Redis Cache Engine](https://book.cakephp.org/5/en/core-libraries/caching.html#redis-cache)
