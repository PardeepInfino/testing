<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Model/Import/Iterator/Stock.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\Import\Iterator;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Xtento\StockImport\Model\Log;

class Stock extends AbstractIterator
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Stock constructor.
     *
     * @param Registry $frameworkRegistry
     * @param ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        Registry $frameworkRegistry,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->registry = $frameworkRegistry;
        $this->objectManager = $objectManager;

        parent::__construct($data);
    }

    public function processUpdates($updatesInFilesToProcess)
    {
        $logEntry = $this->registry->registry('stockimport_log');

        $totalRecordCount = 0;
        $updatedRecordCount = 0;

        $importModel = $this->objectManager->create(
            'Xtento\StockImport\Model\Import\Entity\\' . ucfirst($this->getProfile()->getEntity())
        );
        $importModel->setImportType($this->getImportType());
        $importModel->setTestMode($this->getTestMode());
        $importModel->setProfile($this->getProfile());

        if (!$importModel->prepareImport($updatesInFilesToProcess)) {
            $logEntry->setResult(Log::RESULT_WARNING);
            $logEntry->addResultMessage(
                __(
                    "Files have been parsed, however, the prepareImport function complains that there were problems preparing the import data. Stopping import. Make sure your import processor is set up right."
                )
            );
            return false; // No updates to import.
        }


        foreach ($updatesInFilesToProcess as $updateFile) {
            $path = (isset($updateFile['FILE_INFORMATION']['path'])) ? $updateFile['FILE_INFORMATION']['path'] : '';
            $filename = $updateFile['FILE_INFORMATION']['filename'];
            $sourceId = $updateFile['FILE_INFORMATION']['source_id'];

            $updatesInStockIds = $updateFile['ITEMS'];

            foreach ($updatesInStockIds as $stockId => $updatesToProcess) {
                foreach ($updatesToProcess as $productIdentifier => $updateData) {
                    $totalRecordCount++;
                    try {
                        if (empty($productIdentifier)) {
                            continue;
                        }
                        if (isset($updateData['SKIP_FLAG']) && $updateData['SKIP_FLAG'] === true) {
                            $logEntry->addDebugMessage(
                                __(
                                    "Product with identifier '%1' was skipped because of 'skip' field configuration XML set up in profile.",
                                    str_replace('_SKIP', '', $productIdentifier)
                                )
                            );
                            continue;
                        }

                        $updateResult = $importModel->processItem($productIdentifier, $updateData);

                        if (!$updateResult || isset($updateResult['error'])) {
                            $logEntry->addDebugMessage(
                                __("Notice: %1 | File '%2'", $updateResult['error'], $path . $filename)
                            );
                            continue;
                        } else {
                            if (isset($updateResult['changed']) && $updateResult['changed']) {
                                $updatedRecordCount++;
                            }
                            if (isset($updateResult['debug'])) {
                                $logEntry->addDebugMessage(sprintf("%s", $updateResult['debug'])); // | File '" . $path . $filename . "'", $updateResult['debug']));
                            }
                        }
                    } catch (\Exception $e) {
                        // Don't break execution, but log the error.
                        $logEntry->addDebugMessage(
                            __("Exception catched for row with product identifier '%1' specified in '%2' from source ID '%3':\n%4",
                               $productIdentifier,
                               $path . $filename,
                               $sourceId,
                               $e->getMessage()
                            )
                        );
                        continue;
                    }
                }
            }
        }

        $importModel->afterRun();

        $importResult = ['total_record_count' => $totalRecordCount, 'updated_record_count' => $updatedRecordCount];
        return $importResult;
    }
}