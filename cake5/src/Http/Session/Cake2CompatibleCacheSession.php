<?php
declare(strict_types=1);

namespace App\Http\Session;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use SessionHandlerInterface;

/**
 * CakePHP 2 Compatible Cache Session Handler
 * 
 * This handler provides compatibility with CakePHP 2 session format,
 * which uses double serialization (serialize inside serialize).
 */
class Cake2CompatibleCacheSession implements SessionHandlerInterface
{
    /**
     * Cache configuration name
     *
     * @var string
     */
    protected string $config;

    /**
     * Constructor
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config = [])
    {
        $this->config = $config['config'] ?? 'redis_session';
    }

    /**
     * {@inheritDoc}
     */
    public function open($path, $name): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * 
     * Read session data with CakePHP 2 compatibility
     * CakePHP 2 stores data in format: serialize(session_encode(data))
     * RedisEngine automatically unserializes the data, so we receive the inner session data directly
     */
    public function read($id): string|false
    {
        $data = Cache::read($id, $this->config);

        if ($data === null || $data === false) {
            return '';
        }

        // RedisEngine already unserialized the data
        // We just return it as-is (it's already the session-encoded string)
        if (is_string($data)) {
            return $data;
        }

        return '';
    }

    /**
     * {@inheritDoc}
     * 
     * Write session data in CakePHP 2 compatible format
     * RedisEngine will automatically serialize the data
     */
    public function write($id, $data): bool
    {
        if (empty($data)) {
            return true;
        }

        // Just pass the data to RedisEngine
        // It will automatically serialize it, making it compatible with CakePHP 2
        return Cache::write($id, $data, $this->config);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($id): bool
    {
        return Cache::delete($id, $this->config);
    }

    /**
     * {@inheritDoc}
     */
    public function gc($max_lifetime): int|false
    {
        // CakePHP 5's Cache doesn't have gc method, cleanup is handled automatically
        return 0;
    }
}
