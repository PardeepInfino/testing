<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Query;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Amasty\AltTagGenerator\Api\Data\TemplateInterfaceFactory;

class GetNew implements GetNewInterface
{
    /**
     * @var TemplateInterfaceFactory
     */
    private $templateFactory;

    public function __construct(TemplateInterfaceFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    public function execute(): TemplateInterface
    {
        return $this->templateFactory->create();
    }
}
