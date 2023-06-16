<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Console\Command;

class GeneratorWithoutRedirect extends AbstractGenerator
{
    public const AMMETA_GENERATOR_WITHOUT_REDIRECT = 'ammeta:generate:without-redirect';

    protected function configure(): void
    {
        $this->setName(self::AMMETA_GENERATOR_WITHOUT_REDIRECT);
        $this->setDescription(__('If you don’t need to create redirects.')->render());

        parent::configure();
    }

    protected function isNeedRedirect(): bool
    {
        return false;
    }
}
