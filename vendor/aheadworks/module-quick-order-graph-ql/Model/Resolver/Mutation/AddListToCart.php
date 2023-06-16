<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Aheadworks\QuickOrder\Api\CartManagementInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Class AddListToCart
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class AddListToCart implements ResolverInterface
{
    /**
     * @var CartManagementInterface
     */
    private $cartService;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @param CartManagementInterface $cartService
     * @param DataObjectProcessor $dataObjectProcessor
     * @param GetCartForUser $getCartForUser
     */
    public function __construct(
        CartManagementInterface $cartService,
        DataObjectProcessor $dataObjectProcessor,
        GetCartForUser $getCartForUser
    ) {
        $this->cartService = $cartService;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->getCartForUser = $getCartForUser;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($args['cartId'], $context->getUserId(), $storeId);

        $operationResult = $this->cartService->addListToCart($args['listId'], $cart->getId());
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
        if (empty($args['cartId'])) {
            throw new GraphQlInputException(__('Required parameter "cartId" is missing'));
        }
    }
}
