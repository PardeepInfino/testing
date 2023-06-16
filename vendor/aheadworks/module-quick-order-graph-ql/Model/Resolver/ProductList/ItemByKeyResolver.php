<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrderGraphQl\Model\ProductList\ItemDataProcessor;

/**
 * Class ItemByKeyResolver
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList
 */
class ItemByKeyResolver implements ResolverInterface
{
    /**
     * @var ProductListItemRepositoryInterface
     */
    private $productListItemRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ItemDataProcessor
     */
    private $itemDataProcessor;

    /**
     * @param ProductListItemRepositoryInterface $productListItemRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ItemDataProcessor $itemDataProcessor
     */
    public function __construct(
        ProductListItemRepositoryInterface $productListItemRepository,
        DataObjectProcessor $dataObjectProcessor,
        ItemDataProcessor $itemDataProcessor
    ) {
        $this->productListItemRepository = $productListItemRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->itemDataProcessor = $itemDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);
        $item = $this->productListItemRepository->getByKey($args['itemKey']);
        $itemData = $this->dataObjectProcessor->buildOutputDataArray(
            $item,
            ProductListItemInterface::class
        );
        $websiteId = $context->getExtensionAttributes()->getStore()->getWebsiteId();

        return $this->itemDataProcessor->process($itemData, $websiteId);
    }

    /**
     * Validate arguments
     *
     * @param array $args
     * @throws GraphQlInputException
     */
    private function validateArgs(array $args)
    {
        if (!isset($args['itemKey']) || (isset($args['itemKey']) && ($args['itemKey'] < 0))) {
            throw new GraphQlInputException(__('Please, use correct value for itemKey parameter'));
        }
    }
}
