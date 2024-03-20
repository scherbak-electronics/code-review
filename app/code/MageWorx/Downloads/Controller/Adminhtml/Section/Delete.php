<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Controller\Adminhtml\Section;

class Delete extends Section
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('section_id');
        if ($id) {
            $name = "";
            try {
                $section = $this->sectionRepository->get($id);
                $name    = $section->getName(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                $this->sectionRepository->delete($section);

                $this->messageManager->addSuccessMessage(__('The section %1 has been deleted.', $name));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_section_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_downloads/*/');
            } catch (LocalizedException $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_section_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['section_id' => $id]);
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_section_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['section_id' => $id]);
            }

            return $resultRedirect;
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a section to delete.'));
        $resultRedirect->setPath('mageworx_downloads/*/');

        return $resultRedirect;
    }
}
