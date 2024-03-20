<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Product\Tab;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Ticket
 * @package Cart2Quote\Desk\Block\Product\Tab
 */
class Ticket extends Template implements IdentityInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Product Ticket Tab constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);

        $this->setTabTitle();
    }

    /**
     * Add the success and form blocks as child blocks
     * Add the template
     *
     * @return void
     */
    protected function _beforeToHtml()
    {
        $this->_template = 'Cart2Quote_Desk::product/view/tab/ticket.phtml';

        $this->addChild(
            'ticket.form',
            'Cart2Quote\Desk\Block\Product\Tab\Form'
        );

        $this->addChild(
            'ticket.success',
            'Cart2Quote\Desk\Block\Product\Tab\Success'
        );

        parent::_construct();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $this->setTitle(__("Ask a question"));
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Cart2Quote\Desk\Model\Ticket::CACHE_TAG];
    }
}
