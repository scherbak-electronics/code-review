<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Backend\App\Action\Context;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Api\SectionRepositoryInterface;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \MageWorx\Downloads\Controller\Adminhtml\Section
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param SectionRepositoryInterface $sectionRepository
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        SectionRepositoryInterface $sectionRepository,
        Registry $registry,
        PageFactory $resultPageFactory,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($sectionRepository, $registry, $sectionFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $section = $this->initSection();

            /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();

            $resultPage->setActiveMenu('MageWorx_Downloads::sections');
            $resultPage->getConfig()->getTitle()->set((__('Section')));

            if ($section->getId()) {
                $title = __('Section "%1"', $section->getName(Store::DEFAULT_STORE_ID));
            } else {
                $title = __('New Section');
            }

            $resultPage->getConfig()->getTitle()->prepend($title);

            $data = $this->_getSession()->getData('mageworx_downloads_section_data', true);

            if (!empty($data)) {
                $section->setData($data);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The section no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                'mageworx_downloads/*/index'
            );

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while loading the section page.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                'mageworx_downloads/*/index'
            );

            return $resultRedirect;
        }

        return $resultPage;
    }
}
