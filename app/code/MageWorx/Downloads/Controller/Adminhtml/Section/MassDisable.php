<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use MageWorx\Downloads\Model\Section;

class MassDisable extends MassAction
{
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Section $section
     * @return $this|mixed
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function doTheAction(Section $section)
    {
        $section->setIsActive($this->isActive);
        $this->sectionRepository->save($section);

        return $this;
    }

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 sections have been disabled.', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while disabling sections.');
    }
}
