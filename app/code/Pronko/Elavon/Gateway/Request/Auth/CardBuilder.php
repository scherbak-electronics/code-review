<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Auth;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Converter\CardType;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class CardBuilder
 * @package     Pronko\Elavon\Gateway\Request\Auth
 */
class CardBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const CARD = 'card';
    const NUMBER = 'number';
    const EXPDATE = 'expdate';
    const TYPE = 'type';
    const CHNAME = 'chname';
    const ISSUENO = 'issueno';
    const CVN = 'cvn';
    const PRESIND = 'presind';
    /**#@-*/

    const PRESIND_CVN_PRESENT = '1';
    const PRESIND_CVN_NOT_REQUESTED = '4';

    /**
     * Maximum allowed Credit Card Name length
     */
    const MAX_CREDIT_CARD_NAME_LENGTH = 100;

    /**
     * @var CardType
     */
    private $cardType;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CardBuilder constructor.
     * @param CardType $cardType
     * @param SubjectReader $requestSubjectReader
     * @param ConfigInterface $config
     */
    public function __construct(
        CardType $cardType,
        SubjectReader $requestSubjectReader,
        ConfigInterface $config
    ) {
        $this->cardType = $cardType;
        $this->requestSubjectReader = $requestSubjectReader;
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        $result = [
            self::CARD => [
                self::NUMBER => $payment->getData(DataAssignObserver::CC_NUMBER),
                self::EXPDATE => $this->formatDate($payment),
                self::TYPE => $this->cardType->get($payment->getData(DataAssignObserver::CC_TYPE)),
                self::CHNAME => $this->getCreditCardName($paymentDO)
            ]
        ];
        if ($this->config->getUseCvv()) {
            $result[self::CARD][self::CVN] = [
                self::NUMBER => $payment->getData(DataAssignObserver::CC_CID),
                self::PRESIND => self::PRESIND_CVN_PRESENT
            ];
        } else {
            $result[self::CARD][self::CVN][self::PRESIND] = self::PRESIND_CVN_NOT_REQUESTED;
        }

        $ccSsIssue = $payment->getData(DataAssignObserver::CC_SS_ISSUE);
        if (!empty($ccSsIssue)) {
            $result[self::CARD][self::ISSUENO] = $ccSsIssue;
        }

        return $result;
    }

    /**
     * @param InfoInterface|Payment $payment
     * @return string
     */
    private function formatDate(InfoInterface $payment)
    {
        return sprintf(
            '%02d%02d',
            $payment->getData(DataAssignObserver::CC_EXP_MONTH),
            substr($payment->getData(DataAssignObserver::CC_EXP_YEAR), -2, 2)
        );
    }

    /**
     * @param PaymentDataObjectInterface $paymentDO
     * @return string
     */
    private function getCreditCardName(PaymentDataObjectInterface $paymentDO)
    {
        /** @var AddressAdapterInterface $billingAddress */
        $billingAddress = $paymentDO->getOrder()->getBillingAddress();
        return substr(
            $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            0,
            self::MAX_CREDIT_CARD_NAME_LENGTH
        );
    }
}
