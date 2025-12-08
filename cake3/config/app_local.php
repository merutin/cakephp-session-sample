<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * This file configures Redis cache and session storage for CakePHP 3.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '49ea2abf7f10be831c56b5c0e5b06cc3fc1ead93e8f7e1aa9e43c6fa8c956231'),
    ],

    /*
     * Configure the cache adapters with Redis.
     */
    'Cache' => [
        'default' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'path' => CACHE,
        ],

        /*
         * Configure Redis cache for session storage
         */
        'redis_session' => [
            'className' => 'Cake\Cache\Engine\RedisEngine',
            'prefix' => 'cake_session_',
            'server' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', 6379),
            'persistent' => false,
            'duration' => '+2 hours',
            'database' => 0,
        ],

        /*
         * Configure the cache used for general framework caching.
         */
        '_cake_core_' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'prefix' => 'myapp_cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
        ],

        /*
         * Configure the cache for model and datasource caches.
         */
        '_cake_model_' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'prefix' => 'myapp_cake_model_',
            'path' => CACHE . 'models/',
            'serialize' => true,
            'duration' => '+1 years',
        ],
    ],

    /*
     * Session configuration with Redis backend
     */
    'Session' => [
        'defaults' => 'cache',
        'handler' => [
            'engine' => 'CacheSession',
            'config' => 'redis_session',
        ],
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     */
    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'my_app',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'flags' => [],
            'cacheMetadata' => true,
            'log' => false,
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
        ],
    ],
];
