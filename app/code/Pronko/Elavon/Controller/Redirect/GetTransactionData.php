<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Controller\Redirect;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactoryInterface;

/**
 * Class GetTransactionData
 */
class GetTransactionData extends Action
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var PaymentDataObjectFactoryInterface
     */
    private $paymentDataObjectFactory;

    /**
     * GetTransactionData constructor.
     * @param Context $context
     * @param CommandPoolInterface $commandPool
     * @param Session $checkoutSession
     * @param PaymentDataObjectFactoryInterface $paymentDataObjectFactory
     */
    public function __construct(
        Context $context,
        CommandPoolInterface $commandPool,
        Session $checkoutSession,
        PaymentDataObjectFactoryInterface $paymentDataObjectFactory
    ) {
        parent::__construct($context);
        $this->commandPool = $commandPool;
        $this->session = $checkoutSession;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $orderId = $this->session->getData('last_order_id');

        if (!is_numeric($orderId)) {
            $resultJson->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
            return $resultJson->setData(['message' => __('No such order id.')]);
        }

        $order = $this->session->getLastRealOrder();
        $arguments = [];
        $arguments['payment'] = $this->paymentDataObjectFactory->create($order->getPayment());
        $response = $this->commandPool->get('redirect')->execute($arguments);

        $resultJson->setData($response->get());
        return $resultJson;
    }
}
