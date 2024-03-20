<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Test\Unit\Ui\Component\Post\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Blog\Ui\Component\Post\Listing\Column\Tags;
use Magento\Framework\View\Element\UiComponent\Processor;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Test for \Aheadworks\Blog\Ui\Component\Post\Listing\Column\Tags
 */
class TagsTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Tags constants defined for test
     */
    const TAG1_NAME = 'tag 1';
    const TAG2_NAME = 'tag 2';
    /**#@-*/

    /**
     * @var Tags
     */
    private $column;

    /**
     * @var array
     */
    private $post = [
        'tag_names' => [self::TAG1_NAME, self::TAG2_NAME]
    ];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $processorMock = $this->getMock(
            Processor::class,
            ['register'],
            [],
            '',
            false
        );
        $contextMock = $this->getMockForAbstractClass(ContextInterface::class);
        $contextMock->expects($this->once())
            ->method('getProcessor')
            ->will($this->returnValue($processorMock));

        $this->column = $objectManager->getObject(
            Tags::class,
            ['context' => $contextMock]
        );
    }

    /**
     * Testing of prepareDataSource method
     */
    public function testPrepareDataSource()
    {
        $dataSource = ['data' => ['items' => [$this->post]]];
        $dataSourcePrepared = $this->column->prepareDataSource($dataSource);
        $this->assertEquals(
            self::TAG1_NAME . ', ' . self::TAG2_NAME,
            $dataSourcePrepared['data']['items'][0]['tags']
        );
    }
}
