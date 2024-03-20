<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class UpdateDetailsCommand
 */
class UpdateDetailsCommand implements CommandInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * UpdateDetailsCommand constructor.
     * @param ValidatorInterface $validator
     * @param HandlerInterface $handler
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ValidatorInterface $validator,
        HandlerInterface $handler,
        SubjectReader $subjectReader
    ) {
        $this->validator = $validator;
        $this->handler = $handler;
        $this->subjectReader = $subjectReader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        $response = $this->subjectReader->readResponseObject($commandSubject);

        $result = $this->validator->validate(
            array_merge(
                $commandSubject,
                [
                    'response' => $response
                ]
            )
        );
        if (!$result->isValid()) {
            throw new CommandException(
                __(implode("\n", $result->getFailsDescription()))
            );
        }

        $response = array_change_key_case($response, CASE_LOWER);
        $this->handler->handle($commandSubject, $response);
    }
}
