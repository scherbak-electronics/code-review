<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Test\Unit\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Publish as PublishButton;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button\Publish
 */
class PublishTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    const POST_ID = 1;

    /**
     * @var PublishButton
     */
    private $button;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var PostRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->postRepositoryMock = $this->getMockForAbstractClass(PostRepositoryInterface::class);

        $this->button = $objectManager->getObject(
            PublishButton::class,
            [
                'request' => $this->requestMock,
                'postRepository' => $this->postRepositoryMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('id'))
            ->will($this->returnValue(null));

        $buttonData = $this->button->getButtonData();
        $this->assertTrue(is_array($buttonData));
        $this->assertNotEmpty($buttonData);
    }

    /**
     * Testiong of isVisible method
     *
     * @dataProvider isVisibleDataProvider
     * @param int $postId
     * @param int $postStatus
     * @param bool $expected
     */
    public function testIsVisible($postId, $postStatus, $expected)
    {
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('id'))
            ->will($this->returnValue($postId));
        if ($postId) {
            $postMock = $this->getMockForAbstractClass(PostInterface::class);
            $postMock->expects($this->once())
                ->method('getStatus')
                ->willReturn($postStatus);
            $this->postRepositoryMock->expects($this->any())
                ->method('get')
                ->with($this->equalTo(self::POST_ID))
                ->will($this->returnValue($postMock));
        }

        $class = new \ReflectionClass($this->button);
        $method = $class->getMethod('isVisible');
        $method->setAccessible(true);
        $this->assertEquals($expected, $method->invoke($this->button));
    }

    /**
     * Data provider for testIsVisible method
     *
     * @return array
     */
    public function isVisibleDataProvider()
    {
        return [
            'post Id specified, drafted post' => [self::POST_ID, 'draft', true],
            'post Id specified, published post' => [self::POST_ID, 'publication', false],
            'post Id not specified' => [null, null, true]
        ];
    }
}
