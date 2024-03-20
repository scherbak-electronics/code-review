<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Controller\Adminhtml\Section;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \MageWorx\DownloadsImportExport\Controller\Adminhtml\Section
{
    /**
     * @var \Magento\ImportExport\Helper\Data
     */
    protected $helperImportExport;

    /**
     * ImportExport constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\ImportExport\Helper\Data $helperImportExport
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        \Magento\Framework\Escaper $escaper,
        \Magento\ImportExport\Helper\Data $helperImportExport
    ) {
        $this->helperImportExport = $helperImportExport;
        parent::__construct($context, $fileFactory, $sectionResource, $escaper);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {

        $duplicateNames = $this->sectionResource->getDuplicatedNames();

        if ($duplicateNames) {

            $this->messageManager->addNoticeMessage(
                __(
                    "Before import you should to get away from the section's duplicated names: %1",
                    $this->escaper->escapeHtml(implode(', ', $duplicateNames))
                )
            );

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

            return $resultPage;
        }

        $this->messageManager->addNoticeMessage(
            $this->helperImportExport->getMaxUploadSizeMessage()
        );

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('MageWorx_DownloadsImportExport::sections');
        $resultPage->getConfig()->getTitle()->prepend(__('Downloads: Import Sections'));

        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\DownloadsImportExport\Block\Adminhtml\Section\ImportExportHeader::class
            )
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\DownloadsImportExport\Block\Adminhtml\Section\ImportExport::class
            )
        );

        return $resultPage;
    }
}
