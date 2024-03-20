<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response\Transaction;

use Magento\Framework\DataObjectFactory;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Pronko\Elavon\Api\SettlementRepositoryInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;

/**
 * Class TransactionReportHandler
 */
class TransactionReportHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var SettlementRepositoryInterface
     */
    private $settlementRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * AdditionalInfoHandler constructor.
     * @param SubjectReader $reader
     * @param SettlementRepositoryInterface $settlementRepository
     * @param LoggerInterface $logger
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        SubjectReader $reader,
        SettlementRepositoryInterface $settlementRepository,
        LoggerInterface $logger,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->reader = $reader;
        $this->settlementRepository = $settlementRepository;
        $this->logger = $logger;
        $this->dataObjectFactory = $dataObjectFactory;
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

        $transactionInfo = $payment->getTransactionAdditionalInfo();
        if (isset($transactionInfo['raw_details_info'])) {
            /** @var \Magento\Framework\DataObject $dataObject */
            $dataObject = $this->dataObjectFactory->create(['data' => $transactionInfo['raw_details_info']]);
            try {
                $settlement = $this->settlementRepository->createObject();

                $settlement->setAmount($payment->getAdditionalInformation('last_amount'));
                $settlement->setStatus($payment->getAdditionalInformation('last_transaction_type'));
                $settlement->setOrderId($paymentDO->getOrder()->getOrderIncrementId());
                $settlement->setCurrency($paymentDO->getOrder()->getCurrencyCode());
                $settlement->setTransactionId($dataObject->getData('pasref'));
                $settlement->setAccount($dataObject->getData('account'));
                $settlement->setMessage($dataObject->getData('message'));
                $settlement->setBatchId($dataObject->getData('batchid'));
                $settlement->setAuthCode($dataObject->getData('authcode'));

                $this->settlementRepository->save($settlement);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}
