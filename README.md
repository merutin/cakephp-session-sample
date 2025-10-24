# cakephp-session-sample

CakePHP 2とCakePHP 5でRedisセッションストレージを共有するサンプルアプリケーション

## 概要

このプロジェクトは、CakePHP 2.10.24とCakePHP 5.2を使用し、セッションデータをRedisに保存する環境をDocker Composeで構築します。両バージョンのCakePHPが同じRedisインスタンスを使用してセッションを共有できることを確認できます。

## 必要な環境

- Docker
- Docker Compose

## セットアップ方法

### 1. リポジトリのクローン

```bash
git clone https://github.com/merutin/cakephp-session-sample.git
cd cakephp-session-sample
```

### 2. Docker Composeで起動

```bash
docker-compose up -d
```

初回起動時はイメージのビルドに時間がかかります。

### 3. アプリケーションへのアクセス

ブラウザで以下のURLにアクセスしてください：

#### CakePHP 2
- メインページ: http://localhost:8080
- セッションテストページ: http://localhost:8080/session_test

#### CakePHP 5
- メインページ: http://localhost:8081
- セッションテストページ: http://localhost:8081/session-test

## 構成

### Docker Compose サービス

- **web**: PHP 7.4 + Apache環境でCakePHP 2を実行（ポート8080）
- **cake5**: PHP 8.2 + Apache環境でCakePHP 5を実行（ポート8081）
- **redis**: Redis 6（セッションストレージ用）

### セッション設定

両方のCakePHPアプリケーションが同じRedisインスタンスを使用してセッションを保存します。

#### CakePHP 2の設定
- `cake2/app/Config/bootstrap.php`: Redisキャッシュエンジンの設定
- `cake2/app/Config/core.php`: セッションハンドラーの設定

#### CakePHP 5の設定
- `cake5/config/app_local.php`: Redisキャッシュとセッションの設定

### セッションプレフィックス

両方のアプリケーションで `cake_session_` というプレフィックスを使用しているため、セッションデータを共有できます。

## セッションの動作確認

### CakePHP 2
1. http://localhost:8080/session_test にアクセス
2. ページをリロードすると訪問回数がカウントアップされます

### CakePHP 5
1. http://localhost:8081/session-test にアクセス
2. ページをリロードすると訪問回数がカウントアップされます

### Redisの確認

コンテナ内でRedisに接続してセッションデータを確認できます：

```bash
docker-compose exec redis redis-cli
> KEYS cake_session_*
> GET cake_session_<session_id>
```

## 停止方法

```bash
docker-compose down
```

## ディレクトリ構成

```
.
├── docker-compose.yml       # Docker Compose設定
├── Dockerfile               # CakePHP 2用Dockerfile
├── Dockerfile.cake5         # CakePHP 5用Dockerfile
├── cake2/                   # CakePHP 2.10.24
│   ├── app/
│   │   ├── Config/         # 設定ファイル
│   │   ├── Controller/     # コントローラー
│   │   └── View/           # ビュー
│   └── lib/                # CakePHPライブラリ
├── cake5/                   # CakePHP 5.2
│   ├── config/             # 設定ファイル
│   ├── src/                # ソースコード
│   │   ├── Controller/     # コントローラー
│   │   └── ...
│   ├── templates/          # テンプレート
│   └── webroot/            # 公開ディレクトリ
└── README.md
```

## トラブルシューティング

### パーミッションエラー

#### CakePHP 2
`app/tmp` ディレクトリのパーミッションエラーが発生した場合：

```bash
docker-compose exec web chmod -R 777 /var/www/html/app/tmp
```

#### CakePHP 5
`tmp` または `logs` ディレクトリのパーミッションエラーが発生した場合：

```bash
docker-compose exec cake5 chmod -R 775 /var/www/html/tmp
docker-compose exec cake5 chmod -R 775 /var/www/html/logs
```

### Redisに接続できない

環境変数が正しく設定されているか確認してください：

```bash
docker-compose exec web env | grep REDIS
docker-compose exec cake5 env | grep REDIS
```

## 技術詳細

### CakePHP 2のセッション設定
- Redis拡張: phpredis 5.3.7
- キャッシュエンジン: Redis
- プレフィックス: `cake_session_`

### CakePHP 5のセッション設定
- Redisクライアント: predis/predis 3.2
- キャッシュエンジン: RedisEngine
- プレフィックス: `cake_session_`