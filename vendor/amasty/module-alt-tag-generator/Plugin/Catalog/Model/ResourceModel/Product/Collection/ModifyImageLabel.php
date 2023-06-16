<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Plugin\Catalog\Model\ResourceModel\Product\Collection;

use Amasty\AltTagGenerator\Model\Template\Product\ModifyImageLabels;
use Closure;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class ModifyImageLabel
{
    /**
     * @var ModifyImageLabels
     */
    private $modifyImageLabels;

    public function __construct(ModifyImageLabels $modifyImageLabels)
    {
        $this->modifyImageLabels = $modifyImageLabels;
    }

    public function aroundLoad(ProductCollection $collection, Closure $proceed): ProductCollection
    {
        $isCollectionLoad = $collection->isLoaded();
        $proceed();
        if (!$isCollectionLoad) {
            /** @var Product $product */
            foreach ($collection->getItems() as $product) {
                $this->modifyImageLabels->execute($product);
            }
        }

        return $collection;
    }
}
