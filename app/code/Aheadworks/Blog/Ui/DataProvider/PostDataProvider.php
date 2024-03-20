<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Ui\DataProvider;

use Aheadworks\Blog\Model\ResourceModel\Post\Grid\CollectionFactory;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Post data provider
 */
class PostDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_blog_post');
        if (!empty($dataFromForm)) {
            $object = $this->collection->getNewEmptyItem();
            $object->setData($dataFromForm);
            $data[$object->getId()] = $object->getData();
            $this->dataPersistor->clear('aw_blog_post');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            /** @var \Aheadworks\Blog\Model\Post $post */
            foreach ($this->getCollection()->getItems() as $post) {
                if ($id == $post->getId()) {
                    $data[$id] = $this->prepareFormData($post->getData());
                }
            }
        }
        return $data;
    }

    /**
     * Prepare form data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareFormData(array $itemData)
    {
        $isPublished = $itemData['virtual_status'] == Status::PUBLICATION_PUBLISHED ? 1 : 0;
        $isScheduled = $itemData['virtual_status'] == Status::PUBLICATION_SCHEDULED ? 1 : 0;
        $itemData['is_published'] = $isPublished;
        $itemData['is_not_published'] = $isPublished ? 0 : 1;
        $itemData['is_scheduled'] = $isScheduled;
        $itemData['is_scheduled_post'] = $isScheduled;
        $itemData['has_short_content'] = !empty($itemData['short_content']);
        $itemData['tag_names'] = array_values($itemData['tag_names']);
        return $itemData;
    }
}
