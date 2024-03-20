<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Controller\Adminhtml\Attachment;

class Delete extends Attachment
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('attachment_id');
        $name           = 'unknown';
        if ($id) {
            try {
                $attachment = $this->attachmentRepository->getById($id);
                $name       = $attachment->getName(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                $this->attachmentRepository->delete($attachment);
                $this->messageManager->addSuccessMessage(__('The attachment %1 has been deleted.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_attachment_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_downloads/*/');
            } catch (LocalizedException $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_attachment_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['attachment_id' => $id]);
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_attachment_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );

                $this->messageManager->addErrorMessage('Something went wrong while deleting the attachment.');
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['attachment_id' => $id]);
            }

            return $resultRedirect;
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an attachment to delete.'));
        $resultRedirect->setPath('mageworx_downloads/*/');

        return $resultRedirect;
    }
}
