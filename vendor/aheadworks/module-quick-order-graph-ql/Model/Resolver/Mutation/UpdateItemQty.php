<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Class UpdateItemQty
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class UpdateItemQty implements ResolverInterface
{
    /**
     * @var OperationManager
     */
    private $operationManager;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param OperationManager $operationManager
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        OperationManager $operationManager,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->operationManager = $operationManager;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $operationResult = $this->operationManager->updateItemQty($args['itemKey'], $args['qty'], $storeId);
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
        if (empty($args['itemKey'])) {
            throw new GraphQlInputException(__('Required parameter "itemKey" is missing'));
        }
        if (empty($args['qty'])) {
            throw new GraphQlInputException(__('Required parameter "qty" is missing'));
        }
    }
}
