<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinue
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class SaveAndContinue implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @param RequestInterface $request
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        RequestInterface $request,
        PostRepositoryInterface $postRepository
    ) {
        $this->request = $request;
        $this->postRepository = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'style' => $this->isVisible() ? '' : 'display:none;',
            'sort_order' => 40,
        ];
    }

    /**
     * Checks whether the button should be visible
     *
     * @return bool
     */
    private function isVisible()
    {
        if ($postId = $this->request->getParam('id')) {
            $post = $this->postRepository->get($postId);
            if ($post->getStatus() == PostStatus::PUBLICATION) {
                return true;
            }
        }
        return false;
    }
}
