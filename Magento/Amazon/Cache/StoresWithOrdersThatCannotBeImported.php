<?php

declare(strict_types=1);

namespace Magento\Amazon\Cache;

use Magento\Framework\Serialize\Serializer\Base64Json;

class StoresWithOrdersThatCannotBeImported
{
    private const IDENTIFIER = 'asc_stores_with_incomplete_orders';

    /**
     * @var array|null
     */
    private $accountUuids;
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;
    /**
     * @var Base64Json
     */
    private $base64Json;

    /**
     * @var array|null Internal cache of the last persistent state to reduce redundant writes
     */
    private $persistedState;

    public function __construct(\Magento\Framework\App\CacheInterface $cache, Base64Json $base64Json)
    {
        $this->cache = $cache;
        $this->base64Json = $base64Json;
    }

    public function clean()
    {
        $this->accountUuids = [];
    }

    public function get(): array
    {
        $this->load();
        return $this->accountUuids;
    }

    public function add(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        $this->load();
        $this->accountUuids[$account->getUuid()] = $account->getName();
    }

    public function remove(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        $this->load();
        unset($this->accountUuids[$account->getUuid()]);
    }

    public function persist(): void
    {
        // reduce redundant writes to cache
        if ($this->accountUuids === $this->persistedState) {
            return;
        }
        $this->cache->save($this->base64Json->serialize($this->accountUuids), self::IDENTIFIER);
        $this->persistedState = $this->accountUuids;
    }

    private function load(): void
    {
        if ($this->accountUuids === null) {
            $serializedCacheData = $this->cache->load(self::IDENTIFIER);
            $accountsUuids = $serializedCacheData ? $this->base64Json->unserialize($serializedCacheData) : [];
            $this->accountUuids = $accountsUuids;
            $this->persistedState = $accountsUuids;
        }
    }
}
