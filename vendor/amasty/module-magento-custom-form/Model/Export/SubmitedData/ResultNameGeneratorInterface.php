<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Form Base for Magento 2
 */

namespace Amasty\Customform\Model\Export\SubmitedData;

use Amasty\Customform\Api\Data\AnswerInterface;

interface ResultNameGeneratorInterface
{
    public function generateName(AnswerInterface $answer): string;
}