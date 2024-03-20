<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Cart2Quote\DeskMessageTemplate\Block\Adminhtml\Button
 */
class Delete extends Base implements ButtonProviderInterface
{
    /**
     * @return array
     * @throws LocalizedException
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getMessageId()) {
            return [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete this ticket template?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['message_id' => $this->getMessageId()]);
    }

    /**
     * Return Message ID
     *
     * @return int|null
     * @throws LocalizedException
     */
    public function getMessageId()
    {
        try {
            return $this->messageRepository->getById(
                $this->context->getRequest()->getParam('message_id')
            )->getMessageId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}