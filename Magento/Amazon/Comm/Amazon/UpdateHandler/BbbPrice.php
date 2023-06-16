<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Bestbuybox as BbbResourceModel;

class BbbPrice implements HandlerInterface
{
    /**
     * @var BbbResourceModel
     */
    private $bestbuyboxResource;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(
        BbbResourceModel $bestbuyboxResource,
        ChunkedHandler $chunkedHandler
    ) {
        $this->bestbuyboxResource = $bestbuyboxResource;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $removalsByCountryCode = [];
        $insertions = [];
        foreach ($updates as $logId => $log) {
            $asin = $log['asin'] ?? '';
            if (!$asin) {
                continue;
            }

            $countryCode = $log['country_code'] ?? '';

            if (!$countryCode) {
                continue;
            }

            if (!array_key_exists('landed_price', $log)) {
                $log['landed_price'] = 0.00;
            }
            if (!array_key_exists('list_price', $log)) {
                $log['list_price'] = 0.00;
            }
            if (!array_key_exists('shipping_price', $log)) {
                $log['shipping_price'] = 0.00;
            }

            $removalsByCountryCode[$countryCode][] = $asin;
            $insertions[$logId] = $log;
        }

        foreach ($removalsByCountryCode as $countryCode => $asins) {
            if ($asins) {
                $this->bestbuyboxResource->removeAsinsByCountryCode($countryCode, $asins);
            }
        }

        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->bestbuyboxResource->insert($chunkData);
            },
            $insertions,
            $account,
            'Cannot process logs with best buy box prices. Please report an error.'
        );
    }
}
