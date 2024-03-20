<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use MageWorx\Downloads\Block\Section\Links;

class Sections extends Links implements BlockInterface
{
    /**
     * @return array
     */
    public function prepareIds()
    {
        if ($this->getData('assign_type') === 'all') {
            return [];
        }

        return $this->getData('section_ids') ? explode(',', $this->getData('section_ids')) : [];
    }

    /**
     * @return $this|\MageWorx\Downloads\Block\Links
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}
