<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Gateway\Converter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Gateway\Converter\Amount;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Locale\FormatInterface;

/**
 * Class AmountTest
 */
class AmountTest extends TestCase
{
    /**
     * @var Amount
     */
    private $object;

    /**
     * @var FormatInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $format;

    /**
     * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $localeResolver;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->format = $this->createMock(FormatInterface::class);
        $this->localeResolver = $this->createMock(ResolverInterface::class);

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(
            Amount::class,
            [
                'format' => $this->format,
                'localeResolver' => $this->localeResolver
            ]
        );
    }

    /**
     * @dataProvider amountProvider
     */
    public function testConvert($amount, $expected)
    {
        $result = $this->object->convert($amount);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function amountProvider()
    {
        return [
            [0, 0],
            [100, 10000],
            [99.99, 9999]
        ];
    }

    public function testConvertWithPrecision()
    {
        $amount = 99;
        $expected = 99000;
        $priceFormat = ['precision' => 3];
        $locale = '';
        $this->localeResolver->expects($this->once())
            ->method('emulate')
            ->willReturn($locale);

        $this->localeResolver->expects($this->once())
            ->method('revert');

        $this->format->expects($this->once())
            ->method('getPriceFormat')
            ->with($locale)
            ->willReturn($priceFormat);

        $result = $this->object->convert($amount);

        $this->assertEquals($expected, $result);
    }
}
