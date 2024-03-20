<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Ui\Component\Settlement\Columns;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    const AUTH = 'auth';
    const CAPTURE = 'capture';
    const SETTLE = 'settle';
    const MULTI_SETTLE = 'multisettle';
    const REBATE = 'rebate';
    const VOID = 'void';

    /**
     * @var array
     */
    private $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        foreach ($this->getAvailableStatuses() as $statusCode => $statusName) {
            $this->options[$statusCode]['label'] = $statusName;
            $this->options[$statusCode]['value'] = $statusCode;
        }

        return $this->options;
    }

    /**
     * @return array
     */
    private function getAvailableStatuses()
    {
        return [
            self::AUTH => __('Auth'),
            self::CAPTURE => __('Capture'),
            self::SETTLE => __('Settle'),
            self::MULTI_SETTLE => __('Multi Settle'),
            self::REBATE => __('Rebate'),
            self::VOID => __('Void')
        ];
    }
}
