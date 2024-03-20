<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Validator\Response;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Psr\Log\LoggerInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Gateway\Request\Part\HashBuilder;

/**
 * Class General
 */
class GeneralValidator extends AbstractValidator
{
    /**#@+
     * Response codes constants
     */
    const AUTHORIZED = 0;
    const TRANSACTION_DECLINED = 1;
    const BANK_ERROR = 2;
    const ELAVON_SYSTEM_ERROR = 3;
    const INCORRECT_XML = 5;
    const CLIENT_DEACTIVATED = 6;
    /**#@-*/

    /**
     * Error response key
     */
    const RESULT = 'result';

    /**
     * @var HashEncryptor
     */
    private $hashEncryptor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * ResponseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param HashEncryptor $hashEncryptor
     * @param LoggerInterface $logger
     * @param SubjectReader $reader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        HashEncryptor $hashEncryptor,
        LoggerInterface $logger,
        SubjectReader $reader
    ) {
        $this->hashEncryptor = $hashEncryptor;
        $this->logger = $logger;
        $this->reader = $reader;
        parent::__construct($resultFactory);
    }

    /**
     * Performs domain-related validation for business object
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = $this->reader->readResponse($validationSubject);

        $errorMessages = [];
        if ($this->isSuccess($response) && !$this->isHashMatch($response)) {
            $errorMessages[] = __('Transaction has been declined. Please try again later.');
            $this->logCriticalError($response);
            return $this->createResult(false, $errorMessages);
        }

        if (!$this->isSuccess($response)) {
            return $this->validateError($response, $errorMessages);
        }

        return $this->createResult(true, []);
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isSuccess(array $response)
    {
        return isset($response[self::RESULT]) && $response[self::RESULT] == self::AUTHORIZED;
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isHashMatch(array $response)
    {
        if (isset($response['timestamp']) && isset($response['merchantid'])
            && isset($response['orderid']) && isset($response['result'])
            && isset($response['message']) && isset($response['pasref'])
            && isset($response['authcode'])
        ) {
            $hashString = $this->hashEncryptor->encrypt([
                $response['timestamp'],
                $response['merchantid'],
                $response['orderid'],
                $response['result'],
                $response['message'],
                $response['pasref'],
                $response['authcode']
            ]);

            return isset($response[HashBuilder::HASH_TYPE])
            && $response[HashBuilder::HASH_TYPE] === $hashString;
        }
        return false;
    }

    /**
     * @param array $response
     */
    private function logCriticalError(array $response)
    {
        $message = __('Elavon response error');
        $this->logger->critical($message .  ': ' . var_export($response, true));
    }

    /**
     * @param $response
     * @param $errorMessages
     * @return array
     */
    private function validateError($response, $errorMessages)
    {
        $validationResult = false;
        if (!empty($response[self::RESULT])) {
            $code = $response[self::RESULT];
            $codeType = substr($code, 0, 1);
            switch ($codeType) {
                case self::TRANSACTION_DECLINED:
                    $errorMessages[] = __('Transaction has been declined by Bank. Please try again later.');
                    break;
                case self::BANK_ERROR:
                    $errorMessages[] = __('An error occurred while contacting Bank. Please try again later.');
                    break;
                case self::ELAVON_SYSTEM_ERROR:
                    $errorMessages[] = __(
                        'An error occurred while processing the transaction. Please try again later.'
                    );
                    break;
                case self::INCORRECT_XML:
                    if ($code == 509) {
                        $errorMessages[] = __('Check your Credit Card details and try again')
                            . ': ' . $response['message'];
                    } elseif (in_array($code, [501, 508, 512])) {
                        $errorMessages[] = $response['message'];
                    } else {
                        $errorMessages[] = __(
                            'An error occurred while processing the transaction. Please contact merchant for details.'
                        );
                    }

                    $this->logCriticalError($response);
                    break;
                case self::CLIENT_DEACTIVATED:
                    $errorMessages[] = __(
                        'An error occurred while processing the transaction. Please contact merchant for details.'
                    );
                    $this->logCriticalError($response);
                    break;
            }
            return $this->createResult($validationResult, $errorMessages);
        }
        return $this->createResult($validationResult, $errorMessages);
    }
}
