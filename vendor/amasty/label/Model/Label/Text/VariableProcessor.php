<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text;

class VariableProcessor implements VariableProcessorInterface
{
    public const VARIABLE_REGEXP = '/(?<={)[a-zA-Z0-9_:]+(?=})/';

    public function extractVariables(string $text): array
    {
        preg_match_all(self::VARIABLE_REGEXP, $text, $variables);

        return array_merge(...$variables);
    }

    public function insertVariable(string $text, string $variable, string $variableValue): string
    {
        return str_replace("{{$variable}}", $variableValue, $text);
    }
}