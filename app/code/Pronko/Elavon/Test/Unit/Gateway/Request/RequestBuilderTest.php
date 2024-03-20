<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Request;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderComposite;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Request\RequestBuilder;

/**
 * Class RequestBuilderTest
 */
class RequestBuilderTest extends TestCase
{
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var BuilderComposite | \PHPUnit_Framework_MockObject_MockObject
     */
    private $builderComposite;

    /**
     * @var RequestBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestSubjectReader;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->builderComposite = $this->createMock(BuilderComposite::class);
        $this->requestSubjectReader = $this->createMock(SubjectReader::class);

        $objectManager = new ObjectManager($this);
        $this->requestBuilder = $objectManager->getObject(
            RequestBuilder::class,
            [
                'builderComposite' => $this->builderComposite,
                'requestSubjectReader' => $this->requestSubjectReader,
                'type' => 'test_type'
            ]
        );
    }

    public function testBuild()
    {
        $timestamp = date('YmdHis');
        $result = ['request' => [
            '_attribute' => [
                'type' => 'test_type',
                'timestamp' => $timestamp
            ],
            '_value' => []
        ]];
        $buildSubject = ['request' => ''];

        /** Payment expectations */
        $payment = $this->createMock(Payment::class);
        $payment->expects($this->any())
            ->method('setAdditionalInformation');

        $paymentDO = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDO->expects($this->once())
            ->method('getPayment')
            ->willReturn($payment);

        $this->requestSubjectReader->expects($this->once())
            ->method('readPayment')
            ->with($buildSubject)
            ->willReturn($paymentDO);

        $this->builderComposite->expects($this->once())
            ->method('build')
            ->with($buildSubject)
            ->willReturn([]);

        $this->assertEquals($result, $this->requestBuilder->build($buildSubject));
    }
}
