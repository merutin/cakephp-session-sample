<?php

use function Cake\Core\env;

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
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
        'salt' => env('SECURITY_SALT', '8691f2ae47806053a232b512b99ce4464ccbb8b6cd45050a05367ad4e4215b58'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            /*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',

            'username' => 'my_app',
            'password' => 'secret',

            'database' => 'my_app',
            /*
             * If not using the default 'public' schema with the PostgreSQL driver
             * set it here.
             */
            //'schema' => 'myapp',

            /*
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL', null),
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
            //'schema' => 'myapp',
            'url' => env('DATABASE_TEST_URL', 'sqlite://127.0.0.1/tmp/tests.sqlite'),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    /*
     * Cache configuration for Redis
     * Note: We need to include default cache to not override app.php completely
     */
    'Cache' => [
        'default' => [
            'className' => 'Cake\Cache\Engine\FileEngine',
            'path' => CACHE,
        ],
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

    /*
     * Session configuration - use custom handler for CakePHP 2 compatibility
     */
    'Session' => [
        'defaults' => 'cache',
        'handler' => [
            // 'engine' => 'App\Http\Session\Cake2CompatibleCacheSession',
            'config' => 'redis_session',
        ],
        'cookie' => 'CAKEPHP',
        'timeout' => 120, // 2 hours in minutes
        'cookiePath' => '/',
        'ini' => [
            'session.use_strict_mode' => 0, // Don't reject uninitialized session IDs
        ],
    ],
];
