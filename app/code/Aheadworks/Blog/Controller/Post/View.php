<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Controller\Post;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Post
 */
class View extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $post = $this->postRepository->get(
                $this->getRequest()->getParam('post_id')
            );
            if ($post->getStatus() != Status::PUBLICATION
                || strtotime($post->getPublishDate()) > time()
                || (!in_array($this->getStoreId(), $post->getStoreIds())
                    && !in_array(0, $post->getStoreIds()))
            ) {
                /**  @var \Magento\Framework\Controller\Result\Forward $forward */
                $forward = $this->resultForwardFactory->create();
                return $forward
                    ->setModule('cms')
                    ->setController('noroute')
                    ->forward('index');
            }
            $categoryId = $this->getRequest()->getParam('blog_category_id');
            if ($categoryId && !in_array($categoryId, $post->getCategoryIds())) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->url->getPostUrl($post));
                return $resultRedirect;
            }

            $resultPage = $this->resultPageFactory->create();
            $pageConfig = $resultPage->getConfig();
            $pageConfig->getTitle()->set($post->getTitle());
            $pageConfig->setMetadata('description', $post->getMetaDescription());
            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->goBack();
        }
    }
}
