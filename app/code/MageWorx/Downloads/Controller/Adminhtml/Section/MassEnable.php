<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

class MassEnable extends MassDisable
{
    /**
     * @var bool
     */
    protected $isActive = true;

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 sections have been enabled.', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while enabling sections.');
    }
}
