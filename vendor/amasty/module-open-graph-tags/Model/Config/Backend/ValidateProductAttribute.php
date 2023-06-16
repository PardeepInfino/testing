<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Open Graph Tags for Magento 2 (System)
 */

namespace Amasty\OpenGraphTags\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\NoSuchEntityException;

class ValidateProductAttribute extends Value
{
    public function beforeSave(): Value
    {
        if ($this->isValueChanged()) {
            $value = $this->getValue();
            
            if (is_numeric($value) || !$this->checkIsAttributeValid()) {
                $message = __('Attribute code %1 does not exist', $value);
                $this->setValue($this->getOldValue());
                $this->getData('messageManager')->addErrorMessage($message);
            }
        }

        return parent::beforeSave();
    }

    private function checkIsAttributeValid(): bool
    {
        $isValid = true;
        
        try {
            $this->getData('productAttributeRepository')->get($this->getValue());
        } catch (NoSuchEntityException $e) {
            $isValid = false;
        }
        
        return $isValid;
    }
}
