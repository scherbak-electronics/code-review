<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Quickrfq\Model\Quickrfq\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Status implements OptionSourceInterface
{
    
    protected $_rfq_model;
    
    public function __construct(\FME\Quickrfq\Model\Quickrfq $_rfq_model)
    {
        
        $this->_rfq_model = $_rfq_model;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        $availableOptions = $this->_rfq_model->getAvailableStatuses();
        
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
