<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\Downloads\Api\Data\SectionInterface;
use MageWorx\Downloads\Api\SectionRepositoryInterface;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\Downloads\Controller\Adminhtml\Section as SectionController;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Section;

class InlineEdit extends SectionController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * InlineEdit constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SectionRepositoryInterface $sectionRepository
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SectionRepositoryInterface $sectionRepository,
        JsonFactory $jsonFactory,
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        parent::__construct($sectionRepository, $registry, $sectionFactory, $context);
        $this->jsonFactory           = $jsonFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(['messages' => [__('Please correct the sent data.')], 'error' => true]);
        }

        $this->searchCriteriaBuilder->addFilter('section_id', array_keys($postItems), 'in');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result         = $this->sectionRepository->getList($searchCriteria);

        /** @var SectionInterface $section */
        foreach ($result->getItems() as $section) {
            try {
                $sectionData = $this->filterData($postItems[$section->getId()]);
                $section->addData($sectionData);
                $this->addLocaleToSection($section, $sectionData);

                $this->sectionRepository->save($section);

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithSectionId($section, $e->getMessage());
                $error      = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithSectionId($section, $e->getMessage());
                $error      = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithSectionId(
                    $section,
                    __('Something went wrong while saving the page.')
                );
                $error      = true;
            }
        }

        return $resultJson->setData(['messages' => $messages, 'error' => $error]);
    }

    /**
     * Add section id to error message
     *
     * @param Section $section
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithSectionId(Section $section, $errorText)
    {
        return '[Section ID: ' . $section->getId() . '] ' . $errorText;
    }

    /**
     * @param SectionInterface $section
     * @param array $sectionData
     */
    protected function addLocaleToSection($section, array $sectionData)
    {
        if (array_key_exists('name', $sectionData)) {
            $locales = $section->getStoreLocales();

            foreach ($locales as $locale) {
                if ($locale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    $locale->setStoreName($sectionData['name']);
                }
            }
        }
    }
}
