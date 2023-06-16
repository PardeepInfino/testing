<?php

namespace Magento\Amazon\Model\ReadOnlyMode;

use Magento\Amazon\Cache\InstanceChangeDetectionCache;
use Magento\Framework\FlagManager;
use Magento\Framework\Serialize\Serializer\Base64Json;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class InstanceChangeDetection
{
    private const FLAG_NAME = 'asc_instance_state';

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var InstanceChangeDetectionCache
     */
    private $cache;

    public function __construct(
        FlagManager $flagManager,
        StoreRepositoryInterface $storeRepository,
        Base64Json $serializer,
        InstanceChangeDetectionCache $cache
    ) {
        $this->flagManager = $flagManager;
        $this->storeRepository = $storeRepository;
        $this->serializer = $serializer;
        $this->cache = $cache;
    }

    private function calculateCurrentToken(bool $refreshCachedValue = false): string
    {
        $currentToken = $this->cache->get();
        if ($refreshCachedValue || !$currentToken) {
            $baseUrls = [];
            foreach ($this->storeRepository->getList() as $store) {
                $code = $store->getCode();
                $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, false);
                $secureBaseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true);

                if ($store->isActive()) {
                    $baseUrls[$code] = array_unique(
                        array_map(function (string $url) {
                            return $this->standardizeUrl($url);
                        }, str_replace(['http://', 'https://'], '', [$baseUrl, $secureBaseUrl]))
                    );
                }
            }
            $currentToken = $this->serializer->serialize($baseUrls);
            $this->cache->save($currentToken);
        }

        return $currentToken;
    }

    private function getPersistedToken(): ?string
    {
        return $this->flagManager->getFlagData(self::FLAG_NAME);
    }

    /**
     * Compares two serialized tokens and decides whether a change in a new token considered safe.
     *
     * There are three possible scenarios that we consider safe and one unsafe.
     *
     * Safe scenarios
     *   - A new store has been added
     *   - An old store has been deleted
     *   - A store has not changed
     *
     * Unsafe:
     *   - A store still exists but has changed one of its URLs
     *
     * @param string $oldToken
     * @param string $currentToken
     * @return bool
     */
    private function isTokenChangeSafeToProceed(string $oldToken, string $currentToken): bool
    {
        $oldStores = $this->serializer->unserialize($oldToken);
        $currentStores = $this->serializer->unserialize($currentToken);
        $allCodes = array_unique(array_merge(array_keys($oldStores), array_keys($currentStores)));
        foreach ($allCodes as $storeCode) {
            if (isset($currentStores[$storeCode], $oldStores[$storeCode])
                && $currentStores[$storeCode] !== $oldStores[$storeCode]
            ) {
                /**
                 * This is a little optimization to avoid slash-removal kind of changes to block the store.
                 * We have to keep standardization here for backward compatibility purpose, otherwise
                 * old tokens that has not trimmed URLs wouldn't match. Someday we could remove this code.
                 */
                $standardCurrentURLs = array_map(function (string $url) {
                    return $this->standardizeUrl($url);
                }, $currentStores[$storeCode]);
                $standardOldURLs = array_map(function (string $url) {
                    return $this->standardizeUrl($url);
                }, $oldStores[$storeCode]);
                if ($standardCurrentURLs !== $standardOldURLs) {
                    /*
                     * tokens don't match even after trimming, which means the store has url change.
                     * It's likely a copy of the instance, and we can't proceed
                     */
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Standardize URL to ensure minor changes like trailing slashes are ignored
     *
     * @param string $url
     * @return string
     */
    private function standardizeUrl(string $url): string
    {
        return trim($url, '/');
    }

    public function isNewInstance(): bool
    {
        $persistedToken = $this->getPersistedToken();
        if ($persistedToken === null) {
            $this->refreshPersistedToken();
            return false;
        }
        $currentToken = $this->calculateCurrentToken();
        if ($persistedToken === $currentToken) {
            return false;
        }
        if ($this->isTokenChangeSafeToProceed($persistedToken, $currentToken)) {
            $this->refreshPersistedToken();
            return false;
        }
        return true;
    }

    public function refreshPersistedToken(): void
    {
        $currentToken = $this->calculateCurrentToken(true);
        $this->flagManager->saveFlag(self::FLAG_NAME, $currentToken);
    }
}
