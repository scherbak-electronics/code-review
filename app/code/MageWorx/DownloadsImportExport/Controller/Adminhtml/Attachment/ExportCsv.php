<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCsv extends \MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment
{
    /**
     * @var \MageWorx\DownloadsImportExport\Model\AttachmentCsvExportHandler
     */
    protected $exportHandler;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * ExportCsv constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource
     * @param \MageWorx\DownloadsImportExport\Model\AttachmentCsvExportHandler $exportHandler
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        \MageWorx\DownloadsImportExport\Model\AttachmentCsvExportHandler $exportHandler,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->exportHandler = $exportHandler;
        $this->dateTime      = $dateTime;
        parent::__construct($context, $fileFactory, $sectionResource, $escaper);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $ids    = [];
        $params = $this->getRequest()->getParams();

        if (!empty($params['selected']) && is_array($params['selected'])) {
            $ids = $params['selected'];
            $ids = array_map('intval', $ids);
        }

        $content = $this->exportHandler->getContent($ids);

        return $this->fileFactory->create(
            'export_attachments_file_' . $this->dateTime->gmtTimestamp() . '.csv',
            $content,
            DirectoryList::VAR_DIR
        );
    }
}
