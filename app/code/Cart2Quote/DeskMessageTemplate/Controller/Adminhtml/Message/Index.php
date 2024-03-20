<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

/**
 * Class Index
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class Index extends Message
{
    /**
     * Render message template list view
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Cart2Quote_Desk::desk_message_template')
            ->getConfig()->getTitle()->prepend(__('Manage Messages'));

        return $resultPage;

    }
}
