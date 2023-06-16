<?php
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Aheadworks\QuickOrder\Api\CartManagementInterface;
use Aheadworks\QuickOrder\Model\Url;
use Aheadworks\QuickOrder\Model\ProductList\SessionManager;

/**
 * Class AddToCart
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
class AddToCart extends Action
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartManagementInterface
     */
    private $cartManager;

    /**
     * @var SessionManager
     */
    private $listSessionManager;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param CartManagementInterface $cartManager
     * @param SessionManager $listSessionManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CartManagementInterface $cartManager,
        SessionManager $listSessionManager,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->cartManager = $cartManager;
        $this->listSessionManager = $listSessionManager;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Add the whole list to cart
     *
     * @return ResultInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $list = $this->listSessionManager->getActiveListForCurrentUser();
        $quote = $this->checkoutSession->getQuote();
        if (!$quote->getId()) {
            $this->cartRepository->save($quote);
        }
        $result = $this->cartManager->addListToCart($list->getListId(), $quote->getId());
        $errors = $result->getErrorMessages();
        if (count($errors)) {
            foreach ($errors as $error) {
                $this->messageManager->addWarningMessage($error->getTitle() . ' - ' . $error->getText());
            }
        }
        if (count($result->getSuccessMessages())) {
            $this->messageManager->addSuccessMessage(
                __(
                    'A total of %1 product(s) have been added to your shopping cart.',
                    count($result->getSuccessMessages())
                )
            );
            return $resultRedirect->setPath('checkout/cart');
        }

        return $resultRedirect->setPath(Url::QUICK_ORDER_ROUTE);
    }
}
