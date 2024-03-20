<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Api\SectionRepositoryInterface;
use MageWorx\Downloads\Controller\Adminhtml\Section;
use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory;
use MageWorx\Downloads\Model\Section as SectionModel;

abstract class MassAction extends Section
{
    /**
     *
     * @var Filter
     */
    protected $filter;

    /**
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassAction constructor.
     *
     * @param SectionRepositoryInterface $sectionRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        SectionRepositoryInterface $sectionRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($sectionRepository, $registry, $sectionFactory, $context);
    }

    /**
     * @param SectionModel $section
     * @return mixed
     */
    abstract protected function doTheAction(SectionModel $section);

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->count();

            foreach ($collection as $section) {
                $this->doTheAction($section);
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->getErrorMessage());
        }

        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_downloads/*/index');

        return $redirectResult;
    }

    /**
     * @param $collectionSize
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize)
    {
        return __('Mass action successful on %1 records', $collectionSize);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('Mass action failed');
    }
}
