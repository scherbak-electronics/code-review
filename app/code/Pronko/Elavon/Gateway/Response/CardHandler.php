<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Pronko\Elavon\Gateway\Converter\CardType;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class CardHandler
 */
class CardHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var CardType
     */
    private $cardType;

    /**
     * CaptureHandler constructor.
     * @param SubjectReader $reader
     * @param CardType $cardType
     */
    public function __construct(
        SubjectReader $reader,
        CardType $cardType
    ) {
        $this->reader = $reader;
        $this->cardType = $cardType;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // @codingStandardsIgnoreStart
    public function handle(array $handlingSubject, array $response)
    {// @codingStandardsIgnoreEnd
        $paymentDO = $this->reader->readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $ccType = $this->cardType->get($payment->getData(DataAssignObserver::CC_TYPE));
        $payment->setAdditionalInformation('cc_type', $ccType);
        $payment->setAdditionalInformation(
            'card_number',
            'xxxx-' . $payment->getData(DataAssignObserver::CC_LAST_4)
        );
        $payment->setAdditionalInformation(
            'card_expiry_date',
            sprintf(
                '%s/%s',
                $payment->getData(DataAssignObserver::CC_EXP_MONTH),
                substr($payment->getData(DataAssignObserver::CC_EXP_YEAR), -2, 2)
            )
        );

        $ssStartMonth = $payment->getData(DataAssignObserver::CC_SS_START_MONTH);
        $ssStartYear = $payment->getData(DataAssignObserver::CC_SS_START_YEAR);
        if ($ssStartMonth && $ssStartYear) {
            $payment->setAdditionalInformation(
                'ss_start_date',
                sprintf(
                    '%s/%s',
                    $ssStartMonth,
                    substr($ssStartYear, -2, 2)
                )
            );
        }
    }
}
