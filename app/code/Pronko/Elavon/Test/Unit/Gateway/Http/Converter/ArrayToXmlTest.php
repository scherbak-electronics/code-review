<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Http\Converter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Http\Converter\ArrayToXml;
use Pronko\Elavon\Gateway\Http\Converter\Xml;
use Pronko\Elavon\Gateway\Http\Converter\XmlFactory;

/**
 * Class XmlConverterTest
 */
class ArrayToXmlTest extends TestCase
{
    /**
     * @var ArrayToXml
     */
    private $converter;

    /**
     * @var XmlFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $xmlFactory;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->xmlFactory = $this->createMock(XmlFactory::class);
        $objectManager = new ObjectManager($this);

        $this->converter = $objectManager->getObject(
            ArrayToXml::class,
            [
                'xmlFactory' => $this->xmlFactory
            ]
        );
    }

    public function testConvert()
    {
        $request = ['key' => 'value'];
        $expected = 'object';
        $xmlGenerator = $this->createMock(Xml::class);
        $xmlGenerator->expects($this->once())
            ->method('getDom')
            ->willReturn($expected);
        $xmlGenerator->expects($this->once())
            ->method('arrayToXml')
            ->willReturnSelf();

        $this->xmlFactory->expects($this->once())
            ->method('create')
            ->willReturn($xmlGenerator);

        $result = $this->converter->convert($request);

        $this->assertEquals($expected, $result);
    }
}
