<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Plugin\Adminhtml\Sales;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class CreditmemoPlugin
 * @package     Pronko\Elavon\Plugin\Adminhtml\Sales
 */
class CreditmemoPlugin
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * CreditmemoPlugin constructor.
     * @param ConfigInterface $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // @codingStandardsIgnoreStart
    public function beforeRefund(
        CreditmemoService $subject,
        CreditmemoInterface $creditmemo,
        $offlineRequested = false
    ) {// @codingStandardsIgnoreEnd
        if (!$offlineRequested) {
            $order = $this->orderRepository->get($creditmemo->getOrderId());
            $payment = $order->getPayment();
            if ($payment instanceof OrderPaymentInterface
                && $payment->getMethod() === ConfigInterface::METHOD_CODE
            ) {
                $password = $this->config->getRefundPassword();
                if (empty($password)) {
                    throw new LocalizedException(
                        __(
                            'Refund Password is not set. Please check ' .
                            '<a href="%1" target="_blank">Elavon Credentials</a>.',
                            $this->urlBuilder->getUrl('adminhtml/system_config/edit', ['section' => 'payment'])
                        )
                    );
                }
            }
        }
        return [$creditmemo, $offlineRequested];
    }
}
