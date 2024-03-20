<?php
/**
 *  Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Cart2Quote\Desk\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddAskQuestionsTab
 * @package Cart2Quote\Desk\Observer\Frontend
 */
class AddAskQuestionsTab implements ObserverInterface
{
    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helper;

    /**
     * Class AddAskQuestionsTabs constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Adds the My Tickets to the customer Dashboard
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->isProductInfoDetailsBlock($observer) && $this->helper->getDeskEnabled()
            && $this->helper->getProductPageVisibility()) {
            /** @var \Magento\Catalog\Block\Product\View\Description */
            $block = $observer->getBlock();

            $block->addChild(
                'product.ticket.tab',
                'Cart2Quote\Desk\Block\Product\Tab\Ticket',
                ['sort_order' => 100]
            );

            $block->getLayout()->addToParentGroup(
                'product.info.details.product.ticket.tab',
                'detailed_info'
            );
        }

        return $this;
    }

    /**
     * Check if the block is the Product Info Details Block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     */
    protected function isProductInfoDetailsBlock(\Magento\Framework\Event\Observer $observer)
    {
        return $observer->getBlock()->getNameInLayout() == 'product.info.details';
    }
}
