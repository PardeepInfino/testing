<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterfaceFactory;

/**
 * Class AddItemsToList
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class AddItemsToList implements ResolverInterface
{
    /**
     * @var ProductListManagementInterface
     */
    private $productListService;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ItemDataInterfaceFactory
     */
    private $itemDataFactory;

    /**
     * @param ProductListManagementInterface $productListService
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ItemDataInterfaceFactory $itemDataFactory
     */
    public function __construct(
        ProductListManagementInterface $productListService,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        ItemDataInterfaceFactory $itemDataFactory
    ) {
        $this->productListService = $productListService;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemDataFactory = $itemDataFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        $requestItems = [];
        foreach ($args['itemsData'] as $itemData) {
            /** @var ItemDataInterface $requestItem */
            $requestItem = $this->itemDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $requestItem,
                $itemData,
                ItemDataInterface::class
            );
            $requestItems[] = $requestItem;
        }

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $operationResult = $this->productListService->addItemsToList(
            $args['listId'],
            $requestItems,
            $storeId
        );
        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }

    /**
     * Validate arguments
     *
     * @param array $args
     * @throws GraphQlInputException
     */
    private function validateArgs(array $args)
    {
        if (empty($args['listId'])) {
            throw new GraphQlInputException(__('Required parameter "listId" is missing'));
        }
        if (!isset($args['itemsData']) || !is_array($args['itemsData'])) {
            throw new GraphQlInputException(__('Please, use correct value for "itemsData" parameter'));
        }
    }
}
