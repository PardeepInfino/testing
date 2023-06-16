<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ApiClient;

class VerifyAuthentication
{
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(
        ApiClient $apiClient,
        AccountRepositoryInterface $accountRepository
    ) {
        $this->apiClient = $apiClient;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param string $uuid
     * @return bool
     * @throws ApiClient\ApiException
     * @throws ApiClient\ResponseFormatValidationException
     * @throws ApiClient\ResponseValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function verifyByUuid(string $uuid): bool
    {
        $account = $this->accountRepository->getByUuid($uuid);
        return $this->verifyByAccount($account);
    }

    /**
     * @param \Magento\Amazon\Api\Data\AccountInterface $account
     * @return bool
     * @throws ApiClient\ApiException
     * @throws ApiClient\ResponseValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function verifyByAccount(\Magento\Amazon\Api\Data\AccountInterface $account): bool
    {
        $merchantData = $this->apiClient->getMerchant($account);
        $authenticationStatus = $merchantData['authentication_status'] ?? null;

        if (null !== $authenticationStatus && $account->getAuthenticationStatus() !== $authenticationStatus) {
            $account->setAuthenticationStatus($authenticationStatus);
            $sellerId = $merchantData['seller_id'] ?? null;
            if (!$account->getSellerId() && $sellerId !== null) {
                $account->setSellerId($sellerId);
            }
            $this->accountRepository->save($account);
        }

        return Definitions::ACCOUNT_AUTH_STATUS_AUTHENTICATED === $account->getAuthenticationStatus();
    }
}
