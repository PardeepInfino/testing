<?php
namespace Aheadworks\RequisitionLists\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Modal
 */
class Modal extends Template
{
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
        $result = false;
        $viewModel = $this->getListViewModel();
        $names = $this->getConfigNames();

        if ($names) {
            foreach ($names as $name) {
                $result = $viewModel->getIsEnabledByName($name);

                if (!$result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::toHtml();
    }
}