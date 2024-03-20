<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model\Message\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Cart2Quote\DeskMessageTemplate\Model\Message;

/**
 * Class IsActive
 * @package Cart2Quote\DeskMessageTemplate\Model\Message\Source
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * IsActive constructor.
     *
     * @param Message $message
     */
    public function __construct(
        Message $message
    ) {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $availableStatuses = $this->message->getAvailableStatuses();
        $statuses = [];

        foreach ($availableStatuses as $key => $value) {
            $statuses[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $statuses;
    }
}
