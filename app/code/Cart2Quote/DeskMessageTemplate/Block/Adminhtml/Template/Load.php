<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Template;

use \Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory;
use \Magento\Backend\Block\Template;

/**
 * Class Load
 * @package Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Template
 */
class Load extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * Load constructor.
     *
     * @param Template\Context $context
     * @param CollectionFactory $messageCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $messageCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->messageCollectionFactory = $messageCollectionFactory;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messageCollectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->toOptionHash();
    }
}