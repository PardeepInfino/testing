<?php

namespace Magento\Amazon\Cache;

use Magento\Framework\App\Config;

class InstanceChangeDetectionCache
{
    private const IDENTIFIER = 'asc_current_instance';
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    public function __construct(\Magento\Framework\App\CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function get(): ?string
    {
        return $this->cache->load(self::IDENTIFIER) ?: null;
    }

    public function save(string $value): void
    {
        $this->cache->save($value, self::IDENTIFIER, [Config::CACHE_TAG]);
    }

    public function remove(): void
    {
        $this->cache->remove(self::IDENTIFIER);
    }
}
