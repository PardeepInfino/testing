<?php

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ApiClient;

class UpdateAccount
{
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(ApiClient $apiClient, AccountRepositoryInterface $accountRepository)
    {
        $this->apiClient = $apiClient;
        $this->accountRepository = $accountRepository;
    }

    public function update(Account $account)
    {
        $data = $this->apiClient->getMerchant($account);
        $account->setDataChanges(false);
        if (isset($data['status'], $data['authentication_status'])) {
            $currentStatus = (int)$account->getIsActive();
            $resolvedAccountStatus = $this->convertStatusToInt(
                $data['status'],
                $data['authentication_status'],
                $currentStatus
            );
            if ($resolvedAccountStatus !== $currentStatus) {
                $account->setIsActive((int)$data['status']);
            }
            $account->setAuthenticationStatus($data['authentication_status']);
        }
        if (isset($data['name'])) {
            $account->setName($data['name']);
        }
        if (isset($data['seller_id'])) {
            $account->setSellerId($data['seller_id']);
        }
        if (isset($data['countryCode'])) {
            $account->setCountryCode($data['countryCode']);
        }
        if (isset($data['email'])) {
            $account->setEmail($data['email']);
        }
        if (isset($data['url'])) {
            $account->setBaseUrl($data['url']);
        }
        if ($account->hasDataChanges()) {
            $this->accountRepository->save($account);
        }
    }

    private function convertStatusToInt(string $accountStatus, string $authenticationStatus, int $currentStatus): int
    {
        if ($accountStatus === 'active') {
            return Definitions::ACCOUNT_STATUS_ACTIVE;
        }
        if ($authenticationStatus === Definitions::ACCOUNT_AUTH_STATUS_PENDING_AUTHENTICATION
            || $currentStatus === Definitions::ACCOUNT_STATUS_INCOMPLETE
        ) {
            return Definitions::ACCOUNT_STATUS_INCOMPLETE;
        }
        return Definitions::ACCOUNT_STATUS_INACTIVE;
    }
}
