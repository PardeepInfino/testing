<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\CannotSaveException;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ReadOnlyMode\ReadOnlyConfiguration;

class SetReadOnlyMode implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var ReadOnlyConfiguration
     */
    private $readOnlyConfiguration;

    /**
     * SetReadOnlyMode constructor.
     * @param AscClientLogger $logger
     * @param ReadOnlyConfiguration $readOnlyConfiguration
     */
    public function __construct(
        AscClientLogger $logger,
        ReadOnlyConfiguration $readOnlyConfiguration
    ) {
        $this->logger = $logger;
        $this->readOnlyConfiguration = $readOnlyConfiguration;
    }

    /**
     * @param $parent
     * @param array $args
     * @param Context $context
     * @param ResolveInfo $info
     * @return bool
     * @throws CannotSaveException
     */
    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $isEnabled = $args['enabled'];
        try {
            if ($isEnabled) {
                $this->readOnlyConfiguration->enable();
            } else {
                $this->readOnlyConfiguration->disable();
            }
        } catch (\Throwable $e) {
            $this->logger->error('Cannot save configuration for read-only mode change', ['exception' => $e]);
            throw new CannotSaveException('Cannot save read-only mode setting. Please try again later.', $e);
        }
        return true;
    }
}
