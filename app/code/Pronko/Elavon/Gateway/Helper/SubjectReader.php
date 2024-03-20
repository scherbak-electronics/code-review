<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Helper;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader as MagentoSubjectReader;
use Magento\Framework\DataObject;

/**
 * Class SubjectReader
 */
class SubjectReader
{
    /**
     * @var MagentoSubjectReader
     */
    private $subjectReader;

    /**
     * SubjectReader constructor.
     * @param MagentoSubjectReader $subjectReader
     */
    public function __construct(MagentoSubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Reads response object from subject.
     *
     * @param array $subject
     * @return array
     */
    public function readResponseObject(array $subject)
    {
        return $this->subjectReader->readResponse($subject);
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return $this->subjectReader->readPayment($subject);
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return $this->subjectReader->readAmount($subject);
    }

    /**
     * Reads field from subject
     *
     * @param array $subject
     * @return string
     */
    public function readField(array $subject)
    {
        return $this->subjectReader->readField($subject);
    }

    /**
     * Reads response NVP from subject
     *
     * @param array $subject
     * @return array
     */
    public function readResponse(array $subject)
    {
        return $this->subjectReader->readResponse($subject);
    }

    /**
     * Read state object from subject
     *
     * @param array $subject
     * @return DataObject
     */
    public function readStateObject(array $subject)
    {
        return $this->subjectReader->readStateObject($subject);
    }
}
