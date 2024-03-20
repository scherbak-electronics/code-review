<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment\Widget;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Raw as ResultRaw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\LayoutFactory;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Widget::widget_instance';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     * @param RawFactory $resultRawFactory
     */
    public function __construct(Context $context, LayoutFactory $layoutFactory, RawFactory $resultRawFactory)
    {
        $this->layoutFactory    = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultRaw|ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $this->layoutFactory->create();

        $uniqId          = $this->getRequest()->getParam('uniq_id');
        $attachmentsGrid = $layout->createBlock(
            \MageWorx\Downloads\Block\Adminhtml\Widget\Chooser\Attachments::class,
            '',
            ['data' => ['id' => $uniqId]]
        );

        /** @var ResultRaw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($attachmentsGrid->toHtml());

        return $resultRaw;
    }
}
