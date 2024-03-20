<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Registry for \Aheadworks\Blog\Api\Data\CategoryInterface
 */
class CategoryRegistry
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryDataFactory;

    /**
     * @var array
     */
    private $categoryRegistry = [];

    /**
     * @param EntityManager $entityManager
     * @param CategoryInterfaceFactory $categoryDataFactory
     */
    public function __construct(
        EntityManager $entityManager,
        CategoryInterfaceFactory $categoryDataFactory
    ) {
        $this->entityManager = $entityManager;
        $this->categoryDataFactory = $categoryDataFactory;
    }

    /**
     * Retrieve Category from registry
     *
     * @param int $categoryId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function retrieve($categoryId)
    {
        if (!isset($this->categoryRegistry[$categoryId])) {
            /** @var Category $category */
            $category = $this->categoryDataFactory->create();
            $this->entityManager->load($category, $categoryId);
            if (!$category->getId()) {
                throw NoSuchEntityException::singleField('categoryId', $categoryId);
            } else {
                $this->categoryRegistry[$categoryId] = $category;
            }
        }
        return $this->categoryRegistry[$categoryId];
    }

    /**
     * Remove instance of the Category from registry
     *
     * @param int $categoryId
     * @return void
     */
    public function remove($categoryId)
    {
        if (isset($this->categoryRegistry[$categoryId])) {
            unset($this->categoryRegistry[$categoryId]);
        }
    }

    /**
     * Replace existing Category with a new one
     *
     * @param CategoryInterface $category
     * @return $this
     */
    public function push(CategoryInterface $category)
    {
        if ($categoryId = $category->getId()) {
            $this->categoryRegistry[$categoryId] = $category;
        }
        return $this;
    }
}
