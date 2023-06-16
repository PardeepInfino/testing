<?php

namespace Magento\Amazon\Model\ReadOnlyMode;

use Magento\Config\Model\Config\Factory as ConfigFactory;

class ReadOnlyConfiguration
{
    private const CONFIG_PATH = 'saleschannels/general/read_only';
    private const VALUE_ENABLED = 1;

    /**
     * @var bool|null
     */
    private $isEnabled;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ConfigFactory $configFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configFactory = $configFactory;
    }

    public function isEnabled():bool
    {
        if (null === $this->isEnabled) {
            $this->isEnabled = self::VALUE_ENABLED === (int)$this->scopeConfig->getValue(self::CONFIG_PATH);
        }
        return $this->isEnabled;
    }

    public function enable(): void
    {
        $this->setConfigValue(true);
    }

    public function disable(): void
    {
        $this->setConfigValue(false);
    }

    private function setConfigValue(bool $isEnabled): void
    {
        $config = $this->configFactory->create(
            [
                'data' => [
                    'scope' => 'default',
                    'scope_code' => null,
                ]
            ]
        );
        $config->setDataByPath('saleschannels/general/read_only', (int)$isEnabled);
        $config->save();
        $this->isEnabled = $isEnabled;
    }
}
