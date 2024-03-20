<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Test\Unit\Ui\Component\Settlement\Columns;

use PHPUnit\Framework\TestCase;
use Pronko\Elavon\Ui\Component\Settlement\Columns\Status;

/**
 * Class StatusTest
 * @package     Pronko\Elavon\Test\Unit\Gateway\Converter
 */
class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    private $object;

    protected function setUp() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->object = new Status();
    }

    public function testToOptionsArray()
    {
        $expected = [
            'auth' => [
                'value' => 'auth',
                'label' => __('Auth')
            ],
            'capture' => [
                'value' => 'capture',
                'label' => __('Capture')
            ],
            'settle' => [
                'value' => 'settle',
                'label' => __('Settle')
            ],
            'multisettle' => [
                'value' => 'multisettle',
                'label' => __('Multi Settle')
            ],
            'rebate' => [
                'value' => 'rebate',
                'label' => __('Rebate')
            ],
            'void' => [
                'value' => 'void',
                'label' => __('Void')
            ]
        ];
        $result = $this->object->toOptionArray();

        $this->assertEquals($expected, $result);
    }
}
