<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Model\Redirect\Command;

interface DeleteExpiredRedirectsInterface
{
    /**
     * @return void
     */
    public function execute(): void;
}
