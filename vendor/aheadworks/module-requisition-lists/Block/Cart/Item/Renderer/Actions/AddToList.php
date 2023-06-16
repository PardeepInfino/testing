<?php
namespace Aheadworks\RequisitionLists\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\View\Element\Template;

/**
 * Class AddToList
 */
class AddToList extends Generic
{
    /**
     * AddToList constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        $viewModel = $this->getListViewModel();
        return $viewModel->getIsEnabled();
    }

    /**
     * Check if module is show in cart page
     *
     * @return bool
     */
    public function isShowInCartPage()
    {
        $viewModel = $this->getListViewModel();
        return $viewModel->getIsShowInCartPage();
    }
}