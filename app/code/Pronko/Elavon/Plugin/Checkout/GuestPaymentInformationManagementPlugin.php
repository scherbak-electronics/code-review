<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Plugin\Checkout;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\GuestCartManagementInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Class GuestPaymentInformationManagementPlugin
 * @package     Pronko\Elavon\Plugin\Checkout
 */
class GuestPaymentInformationManagementPlugin
{
    /**
     * @var GuestCartManagementInterface
     */
    private $cartManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GuestPaymentInformationManagementPlugin constructor.
     * @param GuestCartManagementInterface $cartManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        GuestCartManagementInterface $cartManagement,
        LoggerInterface $logger
    ) {
        $this->cartManagement = $cartManagement;
        $this->logger = $logger;
    }

    /**
     * Temporary solution while Magento Team is fixing
     *
     * @param GuestPaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId
     * @param $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     * @throws CouldNotSaveException
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getMethod() !== ConfigInterface::METHOD_CODE) {
            return $proceed($cartId, $email, $paymentMethod, $billingAddress);
        }
        $subject->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new CouldNotSaveException(__('Unable to place order. Please try again later.'), $e);
        }
        return $orderId;
    }
}
