<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Http;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pronko\Elavon\Spi\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Http\Converter\ArrayToXml;
use Pronko\Elavon\Gateway\Http\TransferFactory;

/**
 * Class TransferFactoryTest
 */
class TransferFactoryTest extends TestCase
{
    /**
     * @var TransferFactory
     */
    private $transferFactory;

    /**
     * @var ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var TransferBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    private $transferBuilder;

    /**
     * @var ArrayToXml | \PHPUnit_Framework_MockObject_MockObject
     */
    private $arrayToXml;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->transferBuilder = $this->createMock(TransferBuilder::class);
        $this->arrayToXml = $this->createMock(ArrayToXml::class);

        $objectManager = new ObjectManager($this);
        $this->transferFactory = $objectManager->getObject(
            TransferFactory::class,
            [
                'config' => $this->config,
                'transferBuilder' => $this->transferBuilder,
                'arrayToXml' => $this->arrayToXml
            ]
        );
    }

    public function testCreate()
    {
        $request = [];
        $xml = '<request />';
        $config = ['timeout' => TransferFactory::REQUEST_TIMEOUT, 'verifypeer' => true];
        $gatewayUri = 'https://example.com/test.cgi';

        $xmlStub = $this->createMock(XmlStub::class);
        $xmlStub->expects($this->once())
            ->method('saveXML')
            ->willReturn($xml);

        $this->arrayToXml->expects($this->once())
            ->method('convert')
            ->with($request)
            ->willReturn($xmlStub);

        $transfer = $this->createMock(TransferInterface::class);

        /** Transfer Builder expectations */
        $this->transferBuilder->expects($this->once())->method('setClientConfig')->with($config)->willReturnSelf();
        $this->transferBuilder->expects($this->once())->method('setMethod')->with('POST')->willReturnSelf();
        $this->transferBuilder->expects($this->once())->method('setBody')->with($xml)->willReturnSelf();
        $this->transferBuilder->expects($this->once())->method('setUri')->with($gatewayUri)->willReturnSelf();
        $this->transferBuilder->expects($this->once())->method('build')->willReturn($transfer);

        $this->config->expects($this->once())
            ->method('getGatewayUrl')
            ->willReturn($gatewayUri);

        $result = $this->transferFactory->create($request);
        $this->assertInstanceOf(TransferInterface::class, $result);
    }
}
