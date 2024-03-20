<?php

namespace Customerprice\Attachment\Helper;

use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class GroupCode extends AbstractHelper
{
    protected $customerGroup;

    public function __construct(
        Context $context,
        CustomerGroup $customerGroup
    )
    {
        $this->customerGroup = $customerGroup;
        parent::__construct($context);
    }

    public function getCustomerGroups()
    {
        $customerGroups = [];
        $options = $this->customerGroup->toOptionArray();

        foreach ($options as $option) {
            $customerGroups[$option['label']] = $option['label'];
        }

        return $customerGroups;
    }
}


