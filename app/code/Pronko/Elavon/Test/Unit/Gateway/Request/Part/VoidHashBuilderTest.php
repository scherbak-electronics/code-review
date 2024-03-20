<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Request\Part;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Request\Part\VoidHashBuilder;

/**
 * Class VoidHashBuilderTest
 */
class VoidHashBuilderTest extends TestCase
{
    /**
     * @var VoidHashBuilder
     */
    private $object;

    /**
     * @var HashEncryptor | \PHPUnit_Framework_MockObject_MockObject
     */
    private $hashEncryptor;

    /**
     * @var SubjectReader | \PHPUnit_Framework_MockObject_MockObject
     */
    private $subjectReader;

    /**
     * @var ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->hashEncryptor = $this->createMock(HashEncryptor::class);
        $this->subjectReader = $this->createMock(SubjectReader::class);
        $this->config        = $this->createMock(ConfigInterface::class);

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            VoidHashBuilder::class,
            [
                'hashEncryptor' => $this->hashEncryptor,
                'subjectReader' => $this->subjectReader,
                'config' => $this->config
            ]
        );
    }

    public function testBuild()
    {
        $buildSubject = [];
        $merchantId = 'merchantid';
        $orderPrefix = 'order_prefix';
        $timestamp = time();
        $orderIncrementId = '123';
        $encryptArray = [$timestamp, $merchantId, $orderPrefix . $orderIncrementId, '', '', ''];
        $expectedHash = hash('sha256', implode('.', $encryptArray));
        $expected = ['md5hash' => $expectedHash];

        $order = $this->createMock(OrderAdapterInterface::class);
        $order->expects($this->once())
            ->method('getOrderIncrementId')
            ->willReturn($orderIncrementId);

        $payment = $this->createMock(InfoInterface::class);
        $payment->expects($this->once())
            ->method('getAdditionalInformation')
            ->with('timestamp')
            ->willReturn($timestamp);

        $paymentDO = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDO->expects($this->once())
            ->method('getPayment')
            ->willReturn($payment);
        $paymentDO->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $this->subjectReader->expects($this->once())
            ->method('readPayment')
            ->willReturn($paymentDO);

        $this->config->expects($this->once())
            ->method('getMerchantId')
            ->willReturn($merchantId);
        $this->config->expects($this->once())
            ->method('getOrderPrefix')
            ->willReturn($orderPrefix);

        $this->hashEncryptor->expects($this->once())
            ->method('encrypt')
            ->with($encryptArray)
            ->willReturn($expectedHash);

        $result = $this->object->build($buildSubject);

        $this->assertEquals($expected, $result);
    }
}
