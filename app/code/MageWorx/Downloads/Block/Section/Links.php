<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Section;

class Links extends \MageWorx\Downloads\Block\Links
{
    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttachments()
    {
        if (!$this->attachments) {
            $ids = $this->prepareIds();
            if ($ids !== null) {
                $items      = $this->attachmentManager->getAttachments(
                    $this->getCustomerGroupId(),
                    null,
                    [],
                    $ids
                );
                $inGroupIds = array_keys($items);

                if (!$this->helperData->isHideFiles()) {
                    $items = $this->attachmentManager->getAttachments(null, null, [], $ids);
                }

                $this->prepareAttachments($items, $inGroupIds);
            }
        }

        return $this->attachments;
    }
}
