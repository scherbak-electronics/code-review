<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Model\Attachment;

class MassDelete extends MassAction
{
    /**
     * @param Attachment $attachment
     * @return $this|mixed
     * @throws \Exception
     */
    protected function doTheAction(Attachment $attachment)
    {
        $this->attachmentRepository->delete($attachment);

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
