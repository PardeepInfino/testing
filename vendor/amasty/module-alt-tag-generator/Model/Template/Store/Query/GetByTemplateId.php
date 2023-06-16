<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Store\Query;

use Amasty\AltTagGenerator\Model\ResourceModel\Template\Store\LoadByTemplateId;

class GetByTemplateId implements GetByTemplateIdInterface
{
    /**
     * @var LoadByTemplateId
     */
    private $loadByTemplateId;

    public function __construct(LoadByTemplateId $loadByTemplateIdo)
    {
        $this->loadByTemplateId = $loadByTemplateIdo;
    }

    public function execute(int $templateId): array
    {
        return $this->loadByTemplateId->execute($templateId);
    }
}
