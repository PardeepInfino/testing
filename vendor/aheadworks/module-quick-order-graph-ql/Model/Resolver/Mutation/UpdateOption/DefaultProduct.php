<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Class DefaultProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class DefaultProduct implements ResolverInterface
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

        $buyRequest = [];
        $buyRequest = $this->prepareBuyRequest($buyRequest, $args['optionsData']);
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $operationResult = $this->operationManager->updateItemOption($args['itemKey'], $buyRequest, $storeId);
        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }

    /**
     * Prepare buy request
     *
     * @param array $buyRequest
     * @param array $optionsData
     * @return array
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        if (isset($optionsData['custom_options']) && is_array($optionsData['custom_options'])) {
            $buyRequest['options'] = [];
            foreach ($optionsData['custom_options'] as $customOption) {
                $buyRequest['options'][$customOption['id']] = $customOption['value_string'];
            }
        }

        return $buyRequest;
    }

    /**
     * Validate arguments
     *
     * @param array $args
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if (empty($args['itemKey'])) {
            throw new GraphQlInputException(__('Required parameter "itemKey" is missing'));
        }
        if (!isset($args['optionsData']) || empty($args['optionsData'])) {
            throw new GraphQlInputException(__('Please, use correct value for "optionsData" parameter'));
        }
    }
}
