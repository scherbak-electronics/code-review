<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;

class ExportExamplePost extends \MageWorx\DownloadsImportExport\Controller\Adminhtml\Attachment
{
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * ExportExamplePost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param ComponentRegistrar $componentRegistrar
     * @param \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        ComponentRegistrar $componentRegistrar,
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context, $fileFactory, $sectionResource, $escaper);
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $relativeFilePath = implode(
            DIRECTORY_SEPARATOR,
            [
                'examples',
                'attachment_example_for_import.csv',
            ]
        );
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            'MageWorx_DownloadsImportExport'
        );

        $file = $path . DIRECTORY_SEPARATOR . $relativeFilePath;

        $content = file_get_contents($file);

        return $this->fileFactory->create(
            'attachment_example_for_import.csv',
            $content,
            DirectoryList::VAR_DIR
        );
    }
}
