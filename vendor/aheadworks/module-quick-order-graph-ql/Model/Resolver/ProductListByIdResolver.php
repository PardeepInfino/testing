<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;

/**
 * Class ProductListByIdResolver
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class ProductListByIdResolver implements ResolverInterface
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
        $this->validateArgs($args);
        $productList = $this->productListRepository->get($args['listId']);
        return $this->dataObjectProcessor->buildOutputDataArray(
            $productList,
            ProductListInterface::class
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
        if (!isset($args['listId']) || (isset($args['listId']) && ($args['listId'] < 0))) {
            throw new GraphQlInputException(__('Please, use correct value for listId parameter'));
        }
    }
}
