<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MassDelete
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class MassDelete extends MassActions
{
    /**
     * @param \Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory $collection
     * @return ResponseInterface|ResultInterface|void
     */
    protected function massAction($collection)
    {
        $messagesDeleted = 0;
        foreach ($collection->getAllIds() as $messageId) {
            $this->messageRepository->deleteById($messageId);
            $messagesDeleted++;
        }

        if ($messagesDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $messagesDeleted));
        }
    }
}
