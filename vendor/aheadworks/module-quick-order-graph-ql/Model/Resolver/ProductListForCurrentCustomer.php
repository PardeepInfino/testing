<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\GraphQl\Model\Query\ContextInterface;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;

/**
 * Class ProductListForCurrentCustomer
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class ProductListForCurrentCustomer implements ResolverInterface
{
    /**
     * @var ProductListRepositoryInterface
     */
    private $productListRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param ProductListRepositoryInterface $productListRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ProductListRepositoryInterface $productListRepository,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->productListRepository = $productListRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $productList = $this->productListRepository->getByCustomerId($context->getUserId());
        return $this->dataObjectProcessor->buildOutputDataArray(
            $productList,
            ProductListInterface::class
        );
    }
}
