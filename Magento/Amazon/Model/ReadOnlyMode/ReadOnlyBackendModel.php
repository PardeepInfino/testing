<?php


namespace Magento\Amazon\Model\ReadOnlyMode;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;

class ReadOnlyBackendModel extends Value
{
    /**
     * @var InstanceChangeDetection
     */
    private $instanceChangeDetection;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        InstanceChangeDetection $instanceChangeDetection,
        array $data = []
    ) {
        $this->instanceChangeDetection = $instanceChangeDetection;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function getValue()
    {
        if ($this->instanceChangeDetection->isNewInstance()) {
            return '1';
        }
        return parent::getValue();
    }

    public function afterSave()
    {
        $this->instanceChangeDetection->refreshPersistedToken();
        return parent::afterSave();
    }
}
