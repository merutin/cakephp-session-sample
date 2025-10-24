# cakephp-session-sample

CakePHP 2系でRedisセッションストレージを使用するサンプルアプリケーション

## 概要

このプロジェクトは、CakePHP 2.10.24を使用し、セッションデータをRedisに保存する環境をDocker Composeで構築します。

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

- メインページ: http://localhost:8080
- セッションテストページ: http://localhost:8080/session_test

## 構成

### Docker Compose サービス

- **web**: PHP 7.4 + Apache環境でCakePHP 2を実行
- **redis**: Redis 6（セッションストレージ用）

### セッション設定

セッションデータはRedisに保存されます。設定は以下のファイルで行われています：

- `cake2/app/Config/bootstrap.php`: Redisキャッシュエンジンの設定
- `cake2/app/Config/core.php`: セッションハンドラーの設定

## セッションの動作確認

1. http://localhost:8080/session_test にアクセス
2. ページをリロードすると訪問回数がカウントアップされます
3. セッションデータはRedisに保存されています

### Redisの確認

コンテナ内でRedisに接続してセッションデータを確認できます：

```bash
docker-compose exec redis redis-cli
> KEYS *
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
├── Dockerfile               # PHPコンテナのDockerfile
├── cake2/                   # CakePHP 2.10.24
│   ├── app/
│   │   ├── Config/         # 設定ファイル
│   │   ├── Controller/     # コントローラー
│   │   └── View/           # ビュー
│   └── lib/                # CakePHPライブラリ
└── README.md
```

## トラブルシューティング

### パーミッションエラー

`app/tmp` ディレクトリのパーミッションエラーが発生した場合：

```bash
docker-compose exec web chmod -R 777 /var/www/html/app/tmp
```

### Redisに接続できない

環境変数が正しく設定されているか確認してください：

```bash
docker-compose exec web env | grep REDIS
```