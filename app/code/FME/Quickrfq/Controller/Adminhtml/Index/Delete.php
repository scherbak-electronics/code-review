<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Quickrfq\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Quickrfq::quickrfq');
    }

    /**
     * Delete action
     *
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('quickrfq_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $contact_name = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('FME\Quickrfq\Model\Quickrfq');
                $model->load($id);
                $contact_name = $model->getContactName();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The record has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_delete',
                    ['contact_name' => $contact_name, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_delete',
                    ['contact_name' => $contact_name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['quickrfq_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a record to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
