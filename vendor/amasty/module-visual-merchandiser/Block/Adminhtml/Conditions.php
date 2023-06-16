<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Block\Adminhtml;

class Conditions extends \Magento\Backend\Block\Template
{
    /**
     * @var \Amasty\VisualMerch\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\VisualMerch\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->registry = $registry;
        $this->setTemplate('Amasty_VisualMerch::conditions.phtml');
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getConditionsForm()
    {
        return $this->getLayout()->createBlock(Conditions\Form::class, 'conditions-form');
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getConditionsButton()
    {
        $block = $this->getLayout()
            ->createBlock( \Magento\Backend\Block\Widget\Button::class, 'conditions-button');
        $block->setId('am-apply-conditions')
            ->setLabel(__('Apply Conditions'))
            ->setClass('secondary sort-products');
        return $block;
    }
    
    /**
     * @return string
     */
    public function getConditionFormNewChildUrl()
    {
        $formName = 'category_form';
        $conditionsFieldSetId = $this->ruleFactory->create()->getConditionsFieldSetId($formName);
        return $this->getUrl(
            'amasty_visual_merch/conditions/newConditionHtml/',
            ['form_namespace' => $formName, 'form' => $conditionsFieldSetId]
        );
    }

    /**
     * @return string
     */
    public function getConditionFormImportUrl()
    {
        $params = [];
        if ($category = $this->registry->registry('current_category')) {
            $params['id'] = $category->getId();
        }
        return $this->getUrl('amasty_visual_merch/conditions/import/', $params);
    }
}
