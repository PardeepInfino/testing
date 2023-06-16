<?php

namespace Magento\Amazon\Service;

use Magento\Framework\Exception\LocalizedException;

class ReadOnlyModeException extends LocalizedException implements \GraphQL\Error\ClientAware
{
    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return 'readOnlyModeException';
    }
}
