<!DOCTYPE html>
<html>
<head>
    <title>CakePHP 5 - Redis Session Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #D33C43;
            padding-bottom: 10px;
        }
        .info {
            background-color: #f9f9f9;
            border-left: 4px solid #D33C43;
            padding: 15px;
            margin: 20px 0;
        }
        .count {
            font-size: 48px;
            font-weight: bold;
            color: #D33C43;
            text-align: center;
            margin: 30px 0;
        }
        .session-id {
            font-family: monospace;
            font-size: 14px;
            color: #666;
            word-break: break-all;
        }
        .note {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        a.button {
            display: inline-block;
            background-color: #D33C43;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        a.button:hover {
            background-color: #b32d33;
        }
        .version {
            color: #999;
            font-size: 12px;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CakePHP 5 - Redis Session Test</h1>
        
        <div class="info">
            <strong>Page Visit Count:</strong>
            <div class="count"><?= h($count) ?></div>
        </div>
        
        <div class="info">
            <strong>Session ID:</strong>
            <div class="session-id"><?= h($sessionId) ?></div>
        </div>
        
        <div class="note">
            <p>このページをリロードすると訪問回数がカウントアップされます。</p>
            <p>セッションデータはRedisに <code>cake_session_</code> というprefixで保存されています。</p>
        </div>
        
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="button">Reload Page</a>
        
        <div class="version">
            CakePHP <?= \Cake\Core\Configure::version() ?>
        </div>
    </div>
</body>
</html>
