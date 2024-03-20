<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Controller\Redirect;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactoryInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Psr\Log\LoggerInterface;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class Response
 */
class Response extends Action
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * @var PaymentDataObjectFactoryInterface
     */
    private $paymentDataObjectFactory;

    /**
     * Response constructor.
     * @param Context $context
     * @param CommandPoolInterface $commandPool
     * @param LoggerInterface $logger
     * @param LayoutFactory $layoutFactory
     * @param Session $checkoutSession
     * @param ConfigInterface $config
     * @param ContextHelper $contextHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        CommandPoolInterface $commandPool,
        LoggerInterface $logger,
        LayoutFactory $layoutFactory,
        Session $checkoutSession,
        ConfigInterface $config,
        ContextHelper $contextHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        PaymentDataObjectFactoryInterface $paymentDataObjectFactory
    ) {
        parent::__construct($context);

        $this->commandPool = $commandPool;
        $this->layoutFactory = $layoutFactory;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->orderFactory = $orderFactory;
        $this->contextHelper = $contextHelper;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $resultLayout = $this->layoutFactory->create();
        $resultLayout->addDefaultHandle();
        $processor = $resultLayout->getLayout()->getUpdate();

        $orderId = isset($params['ORDER_ID']) ? $params['ORDER_ID'] : null;
        $orderId = str_replace($this->config->getOrderPrefix(), '', $orderId);

        if (!is_numeric($orderId)) {
            $processor->load(['elavon_response_failure']);
            return $resultLayout;
        }

        $arguments = [];
        $arguments['response'] = $params;

        try {
            $order = $this->orderFactory->create()->loadByIncrementId($orderId);
            $payment = $order->getPayment();
            $this->contextHelper->assertOrderPayment($payment);
            $arguments['payment'] = $this->paymentDataObjectFactory->create($payment);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $processor->load(['elavon_response_failure']);
            return $resultLayout;
        }

        if (!$order) {
            $processor->load(['elavon_response_failure']);
            return $resultLayout;
        }

        try {
            $this->commandPool->get('complete')->execute($arguments);
            $processor->load(['elavon_response_success']);
        } catch (\Exception $exception) {
            $this->commandPool->get('cancel')->execute($arguments);
            $this->logger->critical($exception);
            $processor->load(['elavon_response_failure']);
        }

        return $resultLayout;
    }
}
