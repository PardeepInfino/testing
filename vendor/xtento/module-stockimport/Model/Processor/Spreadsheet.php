<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-08-04T14:40:04+00:00
 * File:          Model/Processor/Spreadsheet.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\Processor;

use Magento\Framework\Exception\LocalizedException;

class Spreadsheet extends AbstractProcessor
{
    protected $config = [];
    protected $headerRow;
    protected $rowData;

    protected function initConfiguration()
    {
        if (!$this->config) {
            # Load configuration:
            $this->config = [
                'IMPORT_SKIP_HEADER' => $this->getConfigValue('skip_header'),
            ];

            if (!class_exists('\PhpOffice\PhpSpreadsheet\Reader\Xls')) {
                throw new LocalizedException(
                    __(
                        'The phpoffice/phpspreadsheet library is not installed. No spreadsheets can be parsed. Please install the library as explained in our wiki using composer in order to use this import processor.'
                    )
                );
            }

            # Get mapping model
            $this->mappingModel = $this->mappingFieldsFactory->create();
            $this->mappingModel->setMappingData($this->getConfigValue('mapping'));

            # Load mapping
            $this->mapping = $this->mappingModel->getMapping();
            if ($this->mappingModel->getMappedFieldsForField('product_identifier') === false) {
                throw new LocalizedException(
                    __(
                        'Please configure the Spreadsheet processor in the configuration section of this import profile. The Product Identifier field may not be empty and must be mapped.'
                    )
                );
            }
        }
    }

    public function getRowsToProcess($filesToProcess)
    {
        $magentoMSISupport = $this->entityHelper->getMagentoMSISupport();

        # Updates to process, later the result
        $updatesInFilesToProcess = [];

        $this->initConfiguration();

        foreach ($filesToProcess as $importFile) {
            $data = $importFile['data'];
            $filename = $importFile['filename'];
            unset($importFile['data']);

            $updatesToProcess = [];
            $foundFields = [];
            $rowCounter = 0;

            $fileExtension = pathinfo($filename)['extension'];
            $tmpFile = tempnam(sys_get_temp_dir(), 'spreadsheet') . $fileExtension;
            file_put_contents($tmpFile, $data);
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tmpFile);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            try {
                $spreadsheet = $reader->load($tmpFile);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                unlink($tmpFile);
                throw new \Exception('Error parsing spreadsheet file: ' . $e->getMessage());
            }

            $worksheet = $spreadsheet->getActiveSheet();
            $this->headerRow = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $this->rowData = $rowData;

                $rowCounter++;
                if ($rowCounter == 1) {
                    // Skip the header line but parse it for field names.
                    $numberOfFields = count($rowData);
                    for ($i = 0; $i < $numberOfFields; $i++) {
                        $this->headerRow[$rowData[$i]] = $i;
                    }
                    if ($this->config['IMPORT_SKIP_HEADER'] == true) {
                        continue;
                    }
                }

                $skipRow = false;
                $stockId = false;
                // First run: Get product identifier for row
                $rowIdentifier = "";
                foreach ($this->mappingModel->getMapping() as $fieldId => $fieldData) {
                    if ($fieldData['field'] == 'product_identifier') {
                        $fieldValue = $this->getFieldData($fieldData);
                        if (!empty($fieldValue)) {
                            $rowIdentifier = $fieldValue;
                        }
                    }
                    if ($fieldData['field'] == 'stock_id' || $fieldData['field'] == 'source_code') {
                        $stockId = $this->getFieldData($fieldData);
                    }
                    // Check if row should be skipped.
                    if (true === $this->fieldsConfiguration->checkSkipImport(
                            $fieldData['field'],
                            $fieldData['config'],
                            $this
                        )
                    ) {
                        $skipRow = true;
                    }
                }
                if (empty($rowIdentifier)) {
                    continue;
                }

                // Check if stock ID was specified, if not, use default stock ID
                if ($magentoMSISupport && empty($stockId)) {
                    $stockId = 'default';
                }
                if (empty($stockId)) {
                    $stockId = 1;
                }
                if (!isset($updatesToProcess[$stockId])) {
                    $updatesToProcess[$stockId] = [];
                }

                if ($skipRow) {
                    $rowIdentifier .= '_SKIP';
                }
                if (!isset($updatesToProcess[$stockId][$rowIdentifier])) {
                    $updatesToProcess[$stockId][$rowIdentifier] = [];
                    $rowArray = [];
                } else {
                    $rowArray = $updatesToProcess[$stockId][$rowIdentifier];
                }

                foreach ($this->mappingModel->getMapping() as $fieldId => $fieldData) {
                    if (isset($fieldData['disabled']) && $fieldData['disabled']) {
                        continue;
                    }
                    $fieldName = $fieldData['field'];
                    $fieldValue = $this->getFieldData($fieldData);
                    if ($fieldValue !== '') {
                        if (!in_array($fieldName, $foundFields)) {
                            $foundFields[] = $fieldName;
                        }
                        if (isset($fieldData['group']) && !empty($fieldData['group'])) {
                            $rowArray[$fieldData['group']][$rowCounter - 1][$fieldName] = $this->mappingModel->formatField(
                                $fieldName,
                                $fieldValue
                            );
                        } else {
                            if ($fieldName == 'qty' && array_key_exists($fieldName, $rowArray)) {
                                // Sum up multiple warehouses qtys for example to $updates
                                $origFieldValue = $fieldValue;
                                $fieldValue = (float)$fieldValue + (float)$rowArray[$fieldName];
                                if ($origFieldValue[0] == '-' || $origFieldValue[0] == '+') {
                                    if ($fieldValue >= 0) {
                                        $fieldValue = "+" . $fieldValue;
                                    } else {
                                        $fieldValue = (string)$fieldValue; // Required so formatField is able to check this properly
                                    }
                                }
                            }
                            $rowArray[$fieldName] = $this->mappingModel->formatField($fieldName, $fieldValue);
                        }
                    }
                }
                if ($skipRow) {
                    // Field in field_configuration XML determined that this row should be skipped. "<skip>" parameter in XML field config
                    $rowArray['SKIP_FLAG'] = true;
                }
                $updatesToProcess[$stockId][$rowIdentifier] = $rowArray;
            }

            // Output the header row in a nicer string
            $hasHeaderRow = ($this->config['IMPORT_SKIP_HEADER']) ? "Yes" : "No";
            $headerRowTemp = $this->headerRow ? $this->headerRow : [];
            array_walk($headerRowTemp, function(&$i, $k) {
                $i = " \"$k\"=\"$i\"";
            });
            // File processed
            $updatesInFilesToProcess[] = [
                "FILE_INFORMATION" => $importFile,
                "HEADER_ROW" => "Skip header row: " . $hasHeaderRow . " | Header row:" . implode("", $headerRowTemp),
                "FIELDS" => $foundFields,
                "ITEMS" => $updatesToProcess
            ];

            unlink($tmpFile);
        }

        /*ini_set('xdebug.var_display_max_depth', 10);
        var_dump($updatesInFilesToProcess);
        die();*/

        return $updatesInFilesToProcess;
    }

    public function getFieldPos($mappedField)
    {
        if (!is_numeric($mappedField) && isset($this->headerRow[$mappedField])) {
            return $this->headerRow[$mappedField];
        } else {
            return $mappedField;
        }
    }

    /**
     * @param $fieldData
     *
     * @return mixed
     * Wrapper function to manipulate field data returned
     */
    public function getFieldData($fieldData)
    {
        $returnData = $this->getFieldDataRaw($fieldData);
        $returnData = $this->fieldsConfiguration->manipulateFieldFetched(
            $fieldData['field'],
            $returnData,
            $fieldData['config'],
            $this
        );
        return $returnData;
    }

    public function getFieldDataRaw($fieldData, $bypassFieldConfiguration = false)
    {
        $field = $fieldData['field'];
        $fieldPos = $this->getFieldPos($fieldData['value']);
        if (isset($this->rowData[$fieldPos])) {
            $data = $this->rowData[$fieldPos];
            if (!$bypassFieldConfiguration) {
                $data = $this->fieldsConfiguration->handleField($field, $data, $fieldData['config']);
            }
            if (($data === '' || $data === null || $data === false) && isset($fieldData['id'])) {
                // Try to get the default value at least.. otherwise ''
                $data = $this->mappingModel->getDefaultValue($fieldData['id']);
            }
        } else {
            if (!$bypassFieldConfiguration) {
                $data = $this->fieldsConfiguration->handleField($field, '', $fieldData['config']);
            } else {
                $data = '';
            }
            if (empty($data) && isset($fieldData['id'])) {
                // Try to get the default value at least.. otherwise ''
                $data = $this->mappingModel->getDefaultValue($fieldData['id']);
            }
        }
        return trim($data);
    }
}
