<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Catalog\Product;

use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use MageWorx\Downloads\Helper\Data as HelperData;

class Attachments extends \MageWorx\Downloads\Block\AttachmentContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @var \MageWorx\Downloads\Model\Attachment\Product
     */
    protected $attachmentProduct;

    /**
     * @var null|\MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    protected $attachmentCollection;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Registry $registry
     * @param HelperData $helperData
     * @param AttachmentProduct $attachmentProduct
     * @param \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory
     * @param \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $registry,
        HelperData $helperData,
        AttachmentProduct $attachmentProduct,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        \MageWorx\Downloads\Api\AttachmentManagerInterface $attachmentManager,
        array $data = []
    ) {
        parent::__construct(
            $helperData,
            $httpContext,
            $context,
            $attachmentCollectionFactory,
            $sectionCollectionFactory,
            $attachmentFactory,
            $attachmentManager,
            $data
        );

        $this->coreRegistry = $registry;
        $this->attachmentProduct = $attachmentProduct;
        $this->attachmentManager = $attachmentManager;
    }

    /**
     *
     * @return string
     */
    public function getSectionTitle()
    {
        return $this->helperData->getProductDownloadsTitle();
    }

    /**
     * Retrieve array of attachment object that allow for view
     *
     * @return array
     */
    public function getAttachments()
    {
        if (!$this->attachments) {

            $attachments = $this->attachmentManager->getAttachments(
                $this->getCustomerGroupId(),
                $this->getProductId()
            );
            $inGroupIds = array_keys($attachments);

            if (!$this->helperData->isHideFiles()) {
                $attachments = $this->attachmentManager->getAttachments(null, $this->getProductId());
            }

            foreach ($attachments as $item) {
                if ($this->isAllowByCustomerGroup($item, $inGroupIds)) {
                    $item->setIsInGroup('1');
                } else {
                    $this->isHasNotAllowedLinks = true;
                }

                $this->attachments[] = $item;
            }
        }

        return $this->attachments;
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('product');

        return $product ? $product->getId() : null;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $template = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class);
        $template->setFragment('catalog.product.list.mageworx.downloads.attachments');

        return parent::_prepareLayout();
    }
}
