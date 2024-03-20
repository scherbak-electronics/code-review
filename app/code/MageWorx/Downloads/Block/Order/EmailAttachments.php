<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Order;

class EmailAttachments extends \MageWorx\Downloads\Block\AttachmentContainer
{
    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $productId = null;

        if ($this->getParentBlock()) {
            $item = $this->getParentBlock()->getItem();
            if ($item) {
                $productId = $item->getProduct()->getId();
            }
        }

        return $productId;
    }

    /**
     * Retrieve array of attachment object that allow for view
     *
     * @return array
     */
    public function getAttachments()
    {
        $attachments = [];

        if (!$this->helperData->isAddToNewOrderEmail()) {
            return $attachments;
        }
        $collection = $this->getAttachmentCollection();

        if (!$collection) {
            return $attachments;
        }

        foreach ($collection->getItems() as $item)
        {
            $item->setIsInGroup(1);

            $attachments[] = $item;
        }

        return $attachments;
    }

    /**
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachmentCollection()
    {
        $productId       = $this->getProductId();
        $customerGroupId = $this->getCustomerGroupId();
        $storeId         = $this->getOrderStoreId();

        if ($productId === null || $customerGroupId === null || $storeId === null) {
            return false;
        }

        $collection = $this->attachmentCollectionFactory->create();
        $collection->getAttachmentsForEmail(
            $this->getProductId(),
            $this->getCustomerGroupId(),
            $this->getOrderStoreId()
        );

        return $collection;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->helperData->getProductDownloadsTitle();
    }

    /**
     * @param \MageWorx\Downloads\Model\Attachment $attachment
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachmentHtml($attachment)
    {
        $block = $this->getLayout()->createBlock(\MageWorx\Downloads\Block\Catalog\Product\Link::class);
        $block->setTemplate('MageWorx_Downloads::email/order/attachment_link.phtml');
        $block->setData('item', $attachment);

        return $block->toHtml();
    }

    /**
     * Get customer group id
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerGroupId()
    {
        if ($itemsBlock = $this->getLayout()->getBlock('items')) {
            if ($order = $itemsBlock->getOrder()) {
                return $order->getCustomerGroupId();
            }
        }

        return null;
    }

    /**
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getOrderStoreId()
    {
        if ($itemsBlock = $this->getLayout()->getBlock('items')) {
            if ($order = $itemsBlock->getOrder()) {
                return $order->getStoreId();
            }
        }

        return null;
    }
}
