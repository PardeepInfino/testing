<?php
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\ProductList;

use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;

/**
 * Converts data to request
 */
class RequestConverter
{
    /**
     * Convert sku list to request items
     *
     * @param string $skuList
     * @return array
     */
    public function convertSkuListToRequestItems(string $skuList): array
    {
        return array_map(
            static function ($item) {
                return [
                    ItemDataInterface::PRODUCT_SKU => trim((string)$item)
                ];
            },
            explode("\n", $skuList)
        );
    }

    /**
     * Convert csv lines to request items
     *
     * @param array $csvLines
     * @return array
     */
    public function convertCsvLinesToRequestItems(array $csvLines): array
    {
        return array_map(
            static function ($item) {
                return [
                    ItemDataInterface::PRODUCT_SKU => trim((string)$item[0]),
                    ItemDataInterface::PRODUCT_QTY => trim((string)$item[1])
                ];
            },
            $this->filterCsvLines($csvLines)
        );
    }

    /**
     * Filter csv lines
     *
     * @param array $csvLines
     * @return array
     */
    private function filterCsvLines(array $csvLines): array
    {
        $result = [];
        foreach ($csvLines as $line) {
            if (isset($line[0])) {
                if (!isset($line[1])) {
                    $line[1] = 1;
                }
                $result[] = $line;
            }
        }

        return $result;
    }
}
