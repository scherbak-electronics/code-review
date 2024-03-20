<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use MageWorx\Downloads\Block\Links;

class Attachments extends Links implements BlockInterface
{
    /**
     * @return array
     */
    public function prepareIds()
    {
        if ($this->getData('assign_type') === 'all') {
            return [];
        }

        return $this->getData('attachment_ids') ? explode(',', $this->getData('attachment_ids')) : [];
    }

    /**
     * @return $this|Links
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}
