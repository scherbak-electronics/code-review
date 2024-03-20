<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;

use Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Cart2Quote\DeskMessageTemplate\Controller\Adminhtml\Message
 */
class Save extends Message
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $id = $this->getRequest()->getParam('message_id');

            if (empty($data['message_id'])) {
                $data['message_id'] = null;
            }

            try {
                /** @var MessageModel $messageModel */
                $messageModel = $this->messageFactory->create();

                if ($id) {
                    try {
                        $messageModel = $this->messageRepository->getById($id);
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage(__('This message no longer exists.'));

                        return $resultRedirect->setPath('*/*/');
                    }
                }

                $messageModel->setData($data);
                $message = $this->messageRepository->save($messageModel);
                $this->messageManager->addSuccessMessage(__('The Message has been saved.'));
                $this->dataPersistor->clear('desk_message_template');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['message_id' => $message->getMessagetId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the message.'));
            }

            $this->dataPersistor->set('desk_message_template', $data);
            
            return $resultRedirect->setPath('*/*/edit', ['message_id' => $this->getRequest()->getParam('message_id')]);
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
