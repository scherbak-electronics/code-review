<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Plugin\Magento\Contact\Block;

/**
 * Class ContactForm
 * @package Cart2Quote\Desk\Plugin\Magento\Contact\Block
 */
class ContactForm extends \Magento\Framework\View\Element\AbstractBlock
{
    private $helper;

    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helper,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Contact\Block\ContactForm $contactForm
     * @param $result
     * @return string
     */
    public function afterGetFormAction(\Magento\Contact\Block\ContactForm $contactForm, $result)
    {
        if ($this->helper->getContactFormVisibility()) {
            return $this->getUrl('desk/ticket/create', ['_secure' => true]);
        }

        return $result;
    }
}
