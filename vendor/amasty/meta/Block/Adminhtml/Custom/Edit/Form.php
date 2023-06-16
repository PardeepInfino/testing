<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Block\Adminhtml\Custom\Edit;

use Amasty\Meta\Api\Data\ConfigInterface;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        '*/*/save',
                        ['id' => $this->getRequest()->getParam('id')]
                    ),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
