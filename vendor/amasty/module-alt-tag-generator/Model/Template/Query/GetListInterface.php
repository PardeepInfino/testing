<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Query;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface GetListInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return TemplateInterface[]
     */
    public function execute(SearchCriteriaInterface $searchCriteria): array;
}
