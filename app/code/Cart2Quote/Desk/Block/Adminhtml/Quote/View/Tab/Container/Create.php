<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container;

/**
 * Class Create
 * @package Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container
 */
class Create extends \Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Create
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Create constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Get the subject
     *
     * @return String
     */
    public function getSubject()
    {
        $subject = '';
        if ($this->getTicket()) {
            $subject = $this->getTicket()->getSubject();
        } elseif ($this->getQuote()) {
            $quotePrefix = $this->getQuote()->getIncrementId();
            if (isset($quotePrefix)) {
                $subject = $quotePrefix;
            }
        }

        return $subject;
    }

    /**
     * Retrieve quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }
}
