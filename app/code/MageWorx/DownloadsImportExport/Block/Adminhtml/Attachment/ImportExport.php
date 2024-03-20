<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Block\Adminhtml\Attachment;

class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'attachment/importExport.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }
}
