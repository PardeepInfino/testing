<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Class RemoveAllItemsFromList
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class RemoveAllItemsFromList implements ResolverInterface
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
     * @param ProductListManagementInterface $productListService
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ProductListManagementInterface $productListService,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->productListService = $productListService;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['listId'])) {
            throw new GraphQlInputException(__('Specify the "listId" value.'));
        }

        $operationResult = $this->productListService->removeAllItemsFromList($args['listId']);
        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }
}
