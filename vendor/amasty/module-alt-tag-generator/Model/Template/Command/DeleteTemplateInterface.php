<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Command;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

interface DeleteTemplateInterface
{
    /**
     * @param TemplateInterface $template
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(TemplateInterface $template): void;
}
