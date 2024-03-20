<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use MageWorx\Downloads\Model\Section;

class MassDelete extends MassAction
{
    /**
     * @param Section $section
     * @return $this|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function doTheAction(Section $section)
    {
        $this->sectionRepository->delete($section);

        return $this;
    }

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 record(s) have been deleted.', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while deleting record(s).');
    }
}
