<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Query;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface GetByIdInterface
{
    /**
     * @param int $id
     * @return TemplateInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $id): TemplateInterface;
}
