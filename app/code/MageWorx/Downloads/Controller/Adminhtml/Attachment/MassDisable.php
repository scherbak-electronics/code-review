<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Model\Attachment;

class MassDisable extends MassAction
{
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Attachment $attachment
     * @return $this
     */
    protected function doTheAction(Attachment $attachment)
    {
        $attachment->setIsActive($this->isActive);
        $this->attachmentRepository->save($attachment);

        return $this;
    }

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('A total of %1 attachments have been disabled.', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('An error occurred while disabling attachments.');
    }
}
