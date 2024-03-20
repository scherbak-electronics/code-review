<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block;

class Links extends \MageWorx\Downloads\Block\AttachmentContainer
{
    /**
     * Prepare URL rewrite editing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('attachment_container.phtml');

        $title = trim((string)$this->getTitle());
        if (empty($title)) {
            $this->setTitle($this->helperData->getFileDownloadsTitle());
        }

        $this->getAttachments();

        return parent::_prepareLayout();
    }

    /**
     * @return array|string
     */
    public function prepareIds()
    {
        $id = $this->getId();

        if ($id === 'all') {
            return [];
        }

        if (empty($id) && $this->getIds()) {
            $id = implode(',', $this->getIds());
        }

        if (empty($id)) {
            return null;
        }

        return explode(',', $id);
    }

    /**
     * @param $items
     * @param $inGroupIds
     */
    public function prepareAttachments($items, $inGroupIds)
    {
        foreach ($items as $item) {

            if ($this->isAllowByCustomerGroup($item, $inGroupIds)) {
                $item->setIsInGroup('1');
            } else {
                $this->isHasNotAllowedLinks = true;
            }
            $this->attachments[] = $item;
        }
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        if (!$this->attachments) {
            $ids = $this->prepareIds();
            if ($ids !== null) {
                $items      = $this->attachmentManager->getAttachments($this->getCustomerGroupId(), null, $ids);
                $inGroupIds = array_keys($items);

                if (!$this->helperData->isHideFiles()) {
                    $items = $this->attachmentManager->getAttachments(null, null, $ids);
                }

                $this->prepareAttachments($items, $inGroupIds);
            }
        }

        return $this->attachments;
    }
}
