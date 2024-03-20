<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 * @package     Pronko\Elavon\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**#@+
     * Payment fields constants
     */
    const CC_NUMBER = 'cc_number';
    const CC_LAST_4 = 'cc_last_4';
    const CC_TYPE = 'cc_type';
    const CC_EXP_MONTH = 'cc_exp_month';
    const CC_EXP_YEAR = 'cc_exp_year';
    const CC_CID = 'cc_cid';
    const CC_SS_ISSUE = 'cc_ss_issue';
    const CC_SS_START_MONTH = 'cc_ss_start_month';
    const CC_SS_START_YEAR = 'cc_ss_start_year';
    /**#@-*/

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * DataAssignObserver constructor.
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = $this->dataObjectFactory->create(['data' => $additionalData]);

        //Magento 2.0.5 support
        $paymentInfo = $observer->getPaymentModel();
        if (!$paymentInfo instanceof InfoInterface) {
            $paymentMethod = $this->readMethodArgument($observer);
            $paymentInfo = $paymentMethod->getInfoInstance();
        }

        if (!$paymentInfo instanceof InfoInterface) {
            throw new LocalizedException(__('Payment Info model does not provided.'));
        }

        $paymentInfo->setData(self::CC_NUMBER, $additionalData->getData('cc_number'));
        $paymentInfo->setData(self::CC_LAST_4, substr($additionalData->getData('cc_number'), -4));
        $paymentInfo->setData(self::CC_TYPE, $additionalData->getData('cc_type'));
        $paymentInfo->setData(self::CC_EXP_MONTH, $additionalData->getData('cc_exp_month'));
        $paymentInfo->setData(self::CC_EXP_YEAR, $additionalData->getData('cc_exp_year'));
        $paymentInfo->setData(self::CC_CID, $additionalData->getData('cc_cid'));
        $paymentInfo->setData(self::CC_SS_ISSUE, $additionalData->getData('cc_ss_issue'));
        $paymentInfo->setData(self::CC_SS_START_MONTH, $additionalData->getData('cc_ss_start_month'));
        $paymentInfo->setData(self::CC_SS_START_YEAR, $additionalData->getData('cc_ss_start_year'));
    }
}
