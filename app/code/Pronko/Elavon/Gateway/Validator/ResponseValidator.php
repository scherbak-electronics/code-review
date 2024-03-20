<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Validator;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Framework\App\Request;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class ResponseValidator
 */
class ResponseValidator extends AbstractValidator
{
    const RESULT = 'RESULT';
    const CODE_AUTHORIZED = '00';

    /**
     * @var Request\Http
     */
    private $request;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader $subjectReader
    ) {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $this->subjectReader->readResponseObject($validationSubject);

        $isValid = true;
        $errorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($response);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                $errorMessages = array_merge($errorMessages, $validationResult[1]);
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }

    /**
     * @return array
     */
    private function getResponseValidators()
    {
        return [
            function (array $response) {
                return [
                    isset($response['RESULT']) && $response['RESULT'] == self::CODE_AUTHORIZED,
                    'Transaction has been declined. Please try again later.'
                ];
            }
        ];
    }
}
