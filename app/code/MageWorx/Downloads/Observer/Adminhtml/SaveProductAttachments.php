<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Observer\Adminhtml;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment;
use Magento\Framework\Registry;

class SaveProductAttachments implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Attachment
     */
    protected $attachmentResource;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SaveProductAttachments constructor.
     *
     * @param RequestInterface $request
     * @param Registry $coreRegistry
     * @param Attachment $attachmentResource
     */
    public function __construct(
        RequestInterface $request,
        Registry $coreRegistry,
        Attachment $attachmentResource
    ) {
        $this->coreRegistry       = $coreRegistry;
        $this->attachmentResource = $attachmentResource;
        $this->request            = $request;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $post    = $this->request->getPostValue('attachments', -1);
        $product = $this->coreRegistry->registry('product');

        if ($product && $product->getId()) {
            $post = !empty($post['attachment']) ? array_column($post['attachment'], 'id') : [];
            $this->attachmentResource->saveAttachmentProductRelation($product, $post);
        }

        return $this;
    }
}
