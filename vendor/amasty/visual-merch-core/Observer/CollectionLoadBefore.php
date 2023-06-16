<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Visual Merchandiser Core by Amasty for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\VisualMerchCore\Observer;

use Amasty\VisualMerchCore\Model\ResourceModel\Product\Collection\AddVisibilityFilter;

class CollectionLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var AddVisibilityFilter
     */
    private $addVisibilityFilter;

    public function __construct(AddVisibilityFilter $addVisibilityFilter)
    {
        $this->addVisibilityFilter = $addVisibilityFilter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $collection = $observer->getEvent()->getDataObject();
        $this->addVisibilityFilter->execute($collection);
    }
}
