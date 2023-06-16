<?php

declare(strict_types=1);

namespace Amasty\RegenerateUrlRewrites\Console\Command\Regenerate;

interface OptionResolverInterface
{
    public const INPUT_KEY_STORE_ID                             = 'store-id';
    public const INPUT_KEY_REGENERATE_ENTITY_TYPE               = 'entity-type';
    public const INPUT_KEY_NO_REINDEX                           = 'no-reindex';
    public const INPUT_KEY_NO_CACHE_FLUSH                       = 'no-cache-flush';
    public const INPUT_KEY_NO_CACHE_CLEAN                       = 'no-cache-clean';
    public const INPUT_KEY_IDS_RANGE                            = 'ids-range';
    public const INPUT_KEY_SPECIFIC_IDS                         = 'ids';
    public const INPUT_KEY_PROCESS_IDENTITY                     = 'process-identity';

    public const DEFAULT_ENTITY_TYPE = 'product';

    /**
     * @return string
     */
    public function getEntity(): string;

    /**
     * @param int $storeId
     * @return array
     */
    public function getEntityIds(int $storeId): array;

    /**
     * @return bool
     */
    public function isRunReindex(): bool;

    /**
     * @return bool
     */
    public function isRunCacheFlush(): bool;

    /**
     * @return bool
     */
    public function isRunCacheClean(): bool;

    /**
     * @return array
     */
    public function getStoresToProcess(): array;

    /**
     * @return string
     */
    public function getProcessIdentity(): string;
}
