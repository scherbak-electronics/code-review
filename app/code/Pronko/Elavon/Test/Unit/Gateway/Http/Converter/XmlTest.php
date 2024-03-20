<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Http\Converter;

use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Http\DomDocumentFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pronko\Elavon\Gateway\Http\Converter\Xml;

/**
 * Class XmlTest
 */
class XmlTest extends TestCase
{
    /**
     * @var Xml
     */
    private $xml;

    /**
     * @var DomDocumentFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $domDocumentFactory;

    /**
     * @var \DOMDocument | \PHPUnit_Framework_MockObject_MockObject
     */
    private $dom;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->dom = $this->getMockBuilder('DOMDocument')
            ->setMethods(
                ['appendChild', 'createElement', 'createTextNode', 'saveXml']
            )->getMock();
        $this->domDocumentFactory = $this->createMock(
            DomDocumentFactory::class);
        $this->domDocumentFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->dom);

        $objectManager = new ObjectManager($this);
        $this->xml = $objectManager->getObject(
            Xml::class,
            [
                'domDocumentFactory' => $this->domDocumentFactory
            ]
        );
    }

    public function testConvert()
    {
        $expected = $this->getXmlContent();
        $domNode = $this->getMockBuilder('DOMNode')
            ->setMethods(['setAttribute', 'appendChild'])->getMock();
        $this->dom->expects($this->any())
            ->method('createElement')
            ->willReturn($domNode);

        $this->dom->expects($this->any())
            ->method('createTextNode')
            ->willReturn($domNode);

        $this->dom->expects($this->any())
            ->method('saveXml')
            ->willReturn($expected);

        $content = $this->getArray();

        $resultDom = $this->xml->arrayToXml($content);

        $this->assertEquals($expected, $resultDom->getDom()->saveXML());
    }

    private function getXmlContent()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<request type="auth" timestamp="20200102180037">
  <md5hash>d105f00f53a6e11decddb353e5833081</md5hash>
  <tssinfo>
    <address type="shipping">
      <code>2</code>
      <country>IE</country>
    </address>
    <address type="billing">
      <code>2</code>
      <country>IE</country>
    </address>
    <custipaddress>127.0.0.1</custipaddress>
  </tssinfo>
  <merchantid>merchantid</merchantid>
  <account>account</account>
  <orderid>000000001</orderid>
  <amount currency="USD">16900</amount>
  <comments>
    <comment id="1">comment 1</comment>
    <comment id="2">comment 2</comment>
  </comments>
  <card>
    <number>4263970000005262</number>
    <expdate>0217</expdate>
    <type>VISA</type>
    <chname>John Doe</chname>
  </card>
  <autosettle flag="0"> </autosettle>
</request>';
    }

    private function getArray()
    {
        return [
            'request' => [
                '_attribute' => [
                    'type' => 'auth',
                    'timestamp' => '20200102180037',
                ],
                '_value' => [
                    'md5hash' => 'd105f00f53a6e11decddb353e5833081',
                    'tssinfo' => [
                        'shipping' => [
                            '_attribute' => [
                                'type' => 'shipping',
                            ],
                            '_value' => [
                                'code' => '2',
                                'country' => 'IE'
                            ],
                            '_name' => 'address'
                        ],
                        'billing' => [
                            '_attribute' => [
                                'type' => 'billing',
                            ],
                            '_value' => [
                                'code' => '2',
                                'country' => 'IE'
                            ],
                            '_name' => 'address'
                        ],
                        'custipaddress' => '127.0.0.1',
                    ],
                    'merchantid' => 'merchantid',
                    'account' => 'account',
                    'orderid' => '000000001',
                    'amount' => [
                        '_value' => '16900',
                        '_attribute' => [
                            'currency' => 'USD'
                        ]
                    ],
                    'comments' => [
                        'comment1' => [
                            '_name'=> 'comment',
                            '_attribute' => [
                                'id' => 1
                            ],
                            '_value' => 'comment 1',
                        ],
                        'comment2' => [
                            '_name'=> 'comment',
                            '_attribute' => [
                                'id' => 2
                            ],
                            '_value' => 'comment 2',
                        ],
                    ],
                    'card' => [
                        'number' => '4263970000005262',
                        'expdate' => '0217',
                        'type' => 'VISA',
                        'chname' => 'John Doe'
                    ],
                    'autosettle' => [
                        '_attribute' => [
                            'flag' => '0'
                        ],
                        '_value' => ' '
                    ]
                ]
            ]
        ];
    }
}
