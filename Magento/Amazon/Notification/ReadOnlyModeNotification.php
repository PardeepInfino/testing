<?php

declare(strict_types=1);

namespace Magento\Amazon\Notification;

use Magento\Amazon\Model\ReadOnlyMode\InstanceChangeDetection;
use Magento\Amazon\Model\ReadOnlyMode\ReadOnlyConfiguration;
use Magento\Amazon\Ui\FrontendUrl;

class ReadOnlyModeNotification implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $acl;
    /**
     * @var ReadOnlyConfiguration
     */
    private $readOnlyConfiguration;
    /**
     * @var InstanceChangeDetection
     */
    private $instanceChangeDetection;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * ReadOnlyModeNotification constructor.
     * @param FrontendUrl $frontendUrl
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param ReadOnlyConfiguration $readOnlyConfiguration
     * @param InstanceChangeDetection $instanceChangeDetection
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        FrontendUrl $frontendUrl,
        \Magento\Framework\AuthorizationInterface $authorization,
        ReadOnlyConfiguration $readOnlyConfiguration,
        InstanceChangeDetection $instanceChangeDetection,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->frontendUrl = $frontendUrl;
        $this->acl = $authorization;
        $this->readOnlyConfiguration = $readOnlyConfiguration;
        $this->instanceChangeDetection = $instanceChangeDetection;
        $this->url = $url;
    }

    public function getIdentity()
    {
        return 'asc-read-only-mode';
    }

    public function isDisplayed()
    {
        return ($this->readOnlyConfiguration->isEnabled() || $this->instanceChangeDetection->isNewInstance())
            && $this->acl->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    public function getText()
    {
        if (!$this->readOnlyConfiguration->isEnabled() && $this->instanceChangeDetection->isNewInstance()) {
            return __(
                'Amazon Sales Channel automatically changed to Read-Only mode due to changes in Store URLs.'
                . ' You can turn off Read-Only mode in the %1.',
                $this->getConfigurationUrlHtml('Configuration')
            )->render();
        }
        return __(
            'Amazon Sales Channel is in Read-Only mode and does not sync any changes back to Amazon.'
            . ' You can turn off Read-Only mode in the %1.',
            $this->getConfigurationUrlHtml('Configuration')
        )->render();
    }

    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_MAJOR;
    }

    /**
     * @param string $linkText
     * @return string
     */
    private function getConfigurationUrlHtml(string $linkText = 'configuration'): string
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $this->url->getUrl(
                'adminhtml/system_config/edit',
                [
                    'section' => 'saleschannels'
                ]
            ),
            $linkText
        );
    }
}
