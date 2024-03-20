<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block;

use Magento\Framework\View\Element\Template;
use MageWorx\Downloads\Helper\Data;
use Magento\Customer\Model\Context as Context;

abstract class AttachmentContainer extends Template
{
    /**
     * @var \MageWorx\Downloads\Helper\Data
     */
    protected $helperData;

    /**
     * @var AttachmentCollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var SectionCollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @var \MageWorx\Downloads\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var boolean
     */
    protected $isHasNotAllowedLinks;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \MageWorx\Downloads\Api\AttachmentManagerInterface
     */
    protected $attachmentManager;

    /**
     * @var array
     */
    protected $attachments = [];


    /**
     * Attachment constructor.
     *
     * @param Data $helperData
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory
     * @param \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory
     * @param \MageWorx\Downloads\Api\AttachmentManagerInterface $attachmentManager
     */
    public function __construct(
        Data $helperData,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Backend\Block\Template\Context $context,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        \MageWorx\Downloads\Api\AttachmentManagerInterface $attachmentManager,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext                 = $httpContext;
        $this->helperData                  = $helperData;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->sectionCollectionFactory    = $sectionCollectionFactory;
        $this->attachmentFactory           = $attachmentFactory;
        $this->attachmentManager           = $attachmentManager;
    }

    /**
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface[]
     */
    abstract public function getAttachments();

    /**
     * Get customer group id
     *
     * @return int
     */
    protected function getCustomerGroupId()
    {
        $customerGroupId = (int)$this->getRequest()->getParam('cid');
        if (!$customerGroupId) {
            $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        }

        return $customerGroupId;
    }

    /**
     *
     * @return boolean
     */
    public function isGroupBySection()
    {
        return $this->helperData->isGroupBySection();
    }

    /**
     *
     * @return boolean
     */
    public function isShowHowToDownloadMessage()
    {
        return $this->isHasNotAllowedLinks && $this->getHowToDownloadMessage();
    }

    /**
     *
     * @return string
     */
    public function getHowToDownloadMessage()
    {
        return $this->helperData->getHowToDownloadMessage();
    }

    /**
     *
     * @param \MageWorx\Downloads\Model\Attachment $attachment
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachmentHtml($attachment)
    {
        $block = $this->getLayout()->createBlock(\MageWorx\Downloads\Block\Catalog\Product\Link::class)
                      ->setTemplate('MageWorx_Downloads::attachment_link.phtml');
        $block->setData('item', $attachment);

        return $block->toHtml();
    }

    /**
     *
     * @param \MageWorx\Downloads\Model\Attachment $item
     * @param array $inGroupIds
     * @return boolean
     */
    protected function isAllowByCustomerGroup($item, $inGroupIds)
    {
        return in_array($item->getId(), $inGroupIds);
    }

    /**
     * Retrieve structured by sections list of attachments object that allow for view
     *
     * @return array
     */
    public function getGroupAttachments()
    {
        $attachments = $this->getAttachments();
        $grouped     = [];

        foreach ($attachments as $attachment) {
            $grouped[$attachment->getSectionId()]['attachments'][] = $attachment;
            $grouped[$attachment->getSectionId()]['title']         = $attachment->getSectionName();
        }

        return $grouped;
    }
}
