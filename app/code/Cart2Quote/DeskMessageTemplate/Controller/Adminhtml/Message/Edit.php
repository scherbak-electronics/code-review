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
 * Class Edit
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class Edit extends Message
{
    /**
     * @return Page|Redirect
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('message_id');
        if ($id) {
            try {
                $message = $this->messageRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while editing the message.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Cart2Quote_Desk::desk_message_template')
            ->getConfig()->getTitle()->prepend(
                $id ? $message->getTitle() : __('New Message')
            );

        return $resultPage;
    }
}
