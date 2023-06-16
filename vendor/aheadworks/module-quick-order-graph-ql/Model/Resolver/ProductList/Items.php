<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrderGraphQl\Model\ProductList\ItemDataProcessor;

/**
 * Class Items
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList
 */
class Items implements ResolverInterface
{
    /**
     * @var ItemDataProcessor
     */
    private $itemDataProcessor;

    /**
     * @param ItemDataProcessor $itemDataProcessor
     */
    public function __construct(
        ItemDataProcessor $itemDataProcessor
    ) {
        $this->itemDataProcessor = $itemDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value[ProductListInterface::ITEMS])) {
            throw new LocalizedException(__('"items" value should be specified'));
        }
        $items = $value[ProductListInterface::ITEMS];

        $itemsData = [];
        $websiteId = $context->getExtensionAttributes()->getStore()->getWebsiteId();
        foreach ($items as &$item) {
            $itemsData[] = $this->itemDataProcessor->process($item, $websiteId);;
        }

        return $itemsData;
    }
}
