<?php

declare(strict_types=1);

namespace Magento\Amazon\Service;

use Magento\Amazon\Model\ReadOnlyMode\InstanceChangeDetection;
use Magento\Amazon\Model\ReadOnlyMode\ReadOnlyConfiguration;

class ReadOnlyMode
{
    /**
     * @var bool|null
     */
    private $isEnabled;

    /**
     * @var InstanceChangeDetection
     */
    private $instanceChangeDetection;
    /**
     * @var ReadOnlyConfiguration
     */
    private $readOnlyConfiguration;

    /**
     * @param InstanceChangeDetection $instanceChangeDetection
     * @param ReadOnlyConfiguration $readOnlyConfiguration
     */
    public function __construct(
        InstanceChangeDetection $instanceChangeDetection,
        ReadOnlyConfiguration $readOnlyConfiguration
    ) {
        $this->instanceChangeDetection = $instanceChangeDetection;
        $this->readOnlyConfiguration = $readOnlyConfiguration;
    }

    public function isEnabled(): bool
    {
        if (null === $this->isEnabled) {
            $isEnabled = $this->readOnlyConfiguration->isEnabled();
            $this->isEnabled = $isEnabled || $this->instanceChangeDetection->isNewInstance();
        }
        return $this->isEnabled;
    }

    /**
     * @param string $message
     * @throws ReadOnlyModeException
     */
    public function assertNotInReadOnlyMode(string $message): void
    {
        if ($this->isEnabled()) {
            throw new ReadOnlyModeException(__($message));
        }
    }
}
