<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Plugin\Catalog\Model\Product;

use Amasty\SeoToolkitLite\Model\Redirect\Product\ProcessBeforeDeletion;
use Magento\Catalog\Model\Product;

class CreateRedirects
{
    /**
     * @var ProcessBeforeDeletion
     */
    private $processBeforeDeletion;
    
    public function __construct(
        ProcessBeforeDeletion $processBeforeDeletion
    ) {
        $this->processBeforeDeletion = $processBeforeDeletion;
    }

    /**
     * @see Product::beforeDelete()
     *
     * @param Product $product
     * @return void
     */
    public function beforeBeforeDelete(Product $product): void
    {
        foreach ($product->getStoreIds() as $storeId) {
            $this->processBeforeDeletion->execute((int)$product->getEntityId(), (int)$storeId);
        }
    }
}
