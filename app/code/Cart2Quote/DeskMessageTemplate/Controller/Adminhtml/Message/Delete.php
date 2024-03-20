<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Delete
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class Delete extends Message
{
    /**
     * @return Page|Redirect
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('message_id');

        if ($id) {
            try {
                $this->messageRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The message has been deleted.'));

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['message_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a message to delete.'));

        return $resultRedirect->setPath('*/*/index');
    }
}
