<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Reorder as SalesReorder;
use Cart2Quote\Not2Order\Helper\Data;

/**
 * Class Reorder
 * @package Cart2Quote\Not2Order\Helper
 */
class Reorder extends SalesReorder
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * Reorder constructor.
     * @param Data $dataHelper
     * @param Context $context
     * @param Session $customerSession
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Data $dataHelper,
        Context $context,
        Session $customerSession,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $customerSession, $orderRepository);
    }

    /**
     * Check is it possible to reorder
     *
     * @param int $orderId
     * @return bool
     */
    public function canReorder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $items = $order->getItems();
        $hideReorder = false;
        $allowedReorder = [];

        if (isset($items)) {
            foreach ($items as $item) {
                $product = $item->getProduct();
                if (isset($product)) {
                    $allowedReorder[] = $this->dataHelper->hideOrderButton($product, $customerGroupId);
                }
            }
            $hideReorder = in_array(false, $allowedReorder);
        }
        if (!$this->isAllowed($order->getStore()) || $hideReorder) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return $order->canReorder();
        } else {
            return true;
        }
    }
}
