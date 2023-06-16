<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\QuickOrder\Model\Config as QuickOrderConfig;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class Config implements ResolverInterface
{
    /**
     * @var QuickOrderConfig
     */
    private $quickOrderConfig;

    /**
     * @param QuickOrderConfig $quickOrderConfig
     */
    public function __construct(
        QuickOrderConfig $quickOrderConfig
    ) {
        $this->quickOrderConfig = $quickOrderConfig;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $websiteId = $args['websiteId'] ?? null;
        return [
            'is_quick_order_enabled' => $this->quickOrderConfig->isEnabled($websiteId),
            'is_add_to_list_button_displayed' => $this->quickOrderConfig->isAddToListButtonDisplayed($websiteId),
            'is_qty_input_displayed' => $this->quickOrderConfig->isQtyInputDisplayed($websiteId)
        ];
    }
}
