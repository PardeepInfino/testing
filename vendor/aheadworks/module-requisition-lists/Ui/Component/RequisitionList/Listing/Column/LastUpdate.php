<?php
namespace Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Listing\Column;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class LastUpdate
 * @package Aheadworks\RequisitionLists\Ui\Component\RequisitionList\Listing\Column
 */
class LastUpdate extends Column
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $localeDate
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $localeDate,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->localeDate = $localeDate;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[RequisitionListInterface::UPDATED_AT] =
                    $this->formatDate($item[RequisitionListInterface::UPDATED_AT]);
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve formatting date
     *
     * @param null|string $date
     * @return string
     * @throws \Exception
     */
    private function formatDate($date)
    {
        $date = new \DateTime($date ?? 'now');
        return $this->localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            null,
            null
        );
    }
}
