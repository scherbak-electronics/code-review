<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Plugin\Magento\SalesRule\Model;

/**
 * Class RulesApplierPlugin
 * @package Cart2Quote\AutoProposal\Plugin\Magento\SalesRule\Model
 */
class RulesApplierPlugin
{
    /**
     * It is needed to reset the Magento shopping cart discount rules when using autoproposal.
     * Otherwise cached discount rules are used.
     *
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     * @return array
     */
    public function beforeApplyRules(
        \Magento\SalesRule\Model\RulesApplier $subject,
        $item,
        $rules,
        $skipValidation,
        $couponCode
    ) {
        if (\Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\Range::$autoProposal) {
            $address = $item->getAddress();
            foreach ($rules as &$rule) {
                $rule->setIsValidForAddress($address, false);
            }
        }

        return [$item, $rules, $skipValidation, $couponCode];
    }
}
