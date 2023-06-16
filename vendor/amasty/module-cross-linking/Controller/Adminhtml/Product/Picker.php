<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Controller\Adminhtml\Product;

class Picker extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Amasty_CrossLinks::seo';

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $productGrid = $this->_view->getLayout()->createBlock(
            \Amasty\CrossLinks\Block\Adminhtml\Link\Edit\Form\Renderer\ProductPicker::class,
            '',
            ['data' => ['id' => $uniqId]]
        );
        $html = $productGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
