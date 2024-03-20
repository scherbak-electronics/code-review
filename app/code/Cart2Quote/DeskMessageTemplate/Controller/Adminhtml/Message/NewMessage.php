<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

/**
 * Class NewMessage
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class NewMessage extends Message
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
