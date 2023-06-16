<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Observer\Backend;

use Amasty\AltTagGenerator\Model\Indexer\Template\ProductProcessor;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var ProductProcessor
     */
    private $productProcessor;

    public function __construct(
        ProductResource $productResource,
        ProductProcessor $productProcessor
    ) {
        $this->productResource = $productResource;
        $this->productProcessor = $productProcessor;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            $this->productResource->addCommitCallback(function () use ($product) {
                $this->productProcessor->reindexRow((int) $product->getEntityId());
            });
        }
    }
}
