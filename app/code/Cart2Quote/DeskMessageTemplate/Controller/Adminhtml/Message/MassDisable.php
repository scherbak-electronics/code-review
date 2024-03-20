<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Cart2Quote\DeskMessageTemplate\Model\Message;

/**
 * Class MassDisable
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class MassDisable extends MassActions
{
    /**
     * @param \Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message\CollectionFactory $collection
     * @return ResponseInterface|ResultInterface|void
     */
    protected function massAction($collection)
    {
        $messagesDisabled = 0;
        foreach ($collection as $message) {
            $messageDataObject = $this->messageRepository->getById($message->getMessageId());
            $messageDataObject->setIsActive(Message::STATUS_DISABLED);
            $this->messageRepository->save($messageDataObject);
            $messagesDisabled++;
        }

        if ($messagesDisabled) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been disabled.', $messagesDisabled));
        }
    }
}
