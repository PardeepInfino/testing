<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2021-03-02T22:25:17+00:00
 * File:          Console/Command/ImportCommand.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Console\Command;

use Magento\Framework\App\State as AppState;
use Magento\Framework\App\AreaList as AreaList;
use Magento\Framework\App\Area as Area;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var AreaList
     */
    protected $areaList;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Xtento\TrackingImport\Model\ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var \Xtento\TrackingImport\Model\ImportFactory
     */
    protected $importFactory;

    /**
     * ImportCommand constructor.
     *
     * @param AppState $appState
     * @param AreaList $areaList
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Xtento\TrackingImport\Model\ProfileFactory $profileFactory
     * @param \Xtento\TrackingImport\Model\ImportFactory $importFactory
     */
    public function __construct(
        AppState $appState,
        AreaList $areaList,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Xtento\TrackingImport\Model\ProfileFactory $profileFactory,
        \Xtento\TrackingImport\Model\ImportFactory $importFactory
    ) {
        $this->appState = $appState;
        $this->areaList = $areaList;
        $this->objectManager = $objectManager;
        $this->profileFactory = $profileFactory;
        $this->importFactory = $importFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('xtento:trackingimport:import')
            ->setDescription('Import XTENTO tracking import profile.')
            ->setDefinition(
                [
                    new InputArgument(
                        'profile',
                        InputArgument::REQUIRED,
                        'Profile IDs to import (multiple IDs: comma-separated). Or specify "list" to list all enabled profiles.'
                    )
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_CRONTAB);
            $configLoader = $this->objectManager->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $this->objectManager->configure($configLoader->load(Area::AREA_CRONTAB));
            $this->areaList->getArea(Area::AREA_CRONTAB)->load(Area::PART_TRANSLATE);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }
        echo(sprintf("[Debug] App Area: %s\n", $this->appState->getAreaCode())); // Required to avoid "area code not set" error

        if ($input->getArgument('profile') === 'list') {
            $output->writeln(sprintf("<info>List of enabled profiles:</info>"));
            $profileCollection = $this->profileFactory->create()->getCollection();
            foreach ($profileCollection as $profile) {
                if (!$profile->getEnabled()) continue;
                $output->writeln(sprintf("<info>- %s (ID: %d)</info>", $profile->getName(), $profile->getId()));
            }
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }

        $profileIds = explode(",", $input->getArgument('profile'));
        if (empty($profileIds)) {
            $output->writeln("<error>Profile IDs to import missing.</error>");
        }

        foreach ($profileIds as $profileId) {
            $profileId = intval($profileId);
            if ($profileId < 1) {
                $output->writeln(sprintf("<error>Invalid profile ID: %d</error>", $profileId));
                continue;
            }

            try {
                $profile = $this->profileFactory->create()->load($profileId);
                if (!$profile->getId()) {
                    $output->writeln(sprintf("<error>Profile ID %d does not exist.</error>", $profileId));
                    continue;
                }
                if (!$profile->getEnabled()) {
                    $output->writeln(sprintf("<error>Profile ID %d is disabled.</error>", $profileId));
                    continue;
                }

                $output->writeln(sprintf("<info>Importing profile ID %d.</info>", $profileId));
                $importModel = $this->importFactory->create()->setProfile($profile);
                // Import
                $importModel->cronImport();
                $output->writeln(sprintf('<info>Import for profile ID %d completed. Check "Execution Log" for detailed results.</info>', $profileId));
            } catch (\Exception $exception) {
                $output->writeln(sprintf("<error>Exception for profile ID %d: %s</error>", $profileId, $exception->getMessage()));
                continue;
            }
        }
        $output->writeln("<info>Finished command.</info>");
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
