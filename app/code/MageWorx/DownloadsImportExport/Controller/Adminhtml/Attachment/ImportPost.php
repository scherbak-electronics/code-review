<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment
{
    /**
     * @var \MageWorx\DownloadsImportExport\Model\AttachmentCsvImportHandler
     */
    protected $csvImportHandler;

    /**
     * ImportPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\DownloadsImportExport\Model\AttachmentCsvImportHandler $csvImportHandler
     * @param \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\DownloadsImportExport\Model\AttachmentCsvImportHandler $csvImportHandler,
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->csvImportHandler = $csvImportHandler;
        parent::__construct($context, $fileFactory, $sectionResource, $escaper);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {

            $file = $this->getRequest()->getFiles('import_attachments_file');
            $isSkipMissedProducts = (bool)$this->getRequest()->getParam('skip_products');
            $isSkipMissedFiles    = (bool)$this->getRequest()->getParam('skip_files');

            if ($file && !empty($file['tmp_name'])) {
                try {
                    $this->csvImportHandler->importFromCsvFile($file, $isSkipMissedProducts, $isSkipMissedFiles);
                    $this->messageManager->addSuccessMessage(__('Attachments were imported.'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->addInvalidFileMessage();
                }
            }
            else {
                $this->addInvalidFileMessage();
            }
        }
        else {
            $this->addInvalidFileMessage();
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }

    /**
     * @return void
     */
    protected function addInvalidFileMessage()
    {
        $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
    }
}
