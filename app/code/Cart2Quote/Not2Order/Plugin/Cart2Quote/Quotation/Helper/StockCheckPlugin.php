<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Not2Order\Plugin\Cart2Quote\Quotation\Helper;

/**
 * Class StockCheckPlugin
 *
 * @package Cart2Quote\Not2Order\Plugin\Cart2Quote\Quotation\Helper
 */
class StockCheckPlugin extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Checkout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Cart2Quote\Not2Order\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * StockCheckPlugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Cart2Quote\Quotation\Helper\StockCheck $subject
     * @param array $result
     * @return array
     * @throws \Exception
     */
    public function afterIsMoveToCartAllowed(\Cart2Quote\Quotation\Helper\StockCheck $subject, $result)
    {
        if ($this->moduleManager->isOutputEnabled('Cart2Quote_Not2Order')) {
            $quotationSession = $quotationSession = \Magento\Framework\App\ObjectManager::getInstance()->create(
                \Cart2Quote\Quotation\Model\Session::class
            );

            $customerGroup = $this->dataHelper->getCustomerGroupId();
            $quotationQuote = $quotationSession->getQuote();
            $visibleItems = $quotationQuote->getAllVisibleItems();

            foreach ($visibleItems as $visibleItem) {
                $product = $visibleItem->getProduct();
                $hideOrderButton = $this->dataHelper->hideOrderButton($product, $customerGroup);

                if (!$hideOrderButton) {
                    throw new \Exception(
                        __(
                            "The product '%1' is not orderable and cannot be added to your shopping cart.",
                            $product->getName()
                        )
                    );
                }
            }
        }

        return [$result];
    }
}
