<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Backend\Template\Initialization;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

interface ProcessorInterface
{
    /**
     * @param TemplateInterface $template
     * @param array $inputTemplateData
     * @return void
     * @throws LocalizedException
     * @throws InputException
     */
    public function execute(TemplateInterface $template, array $inputTemplateData): void;
}
