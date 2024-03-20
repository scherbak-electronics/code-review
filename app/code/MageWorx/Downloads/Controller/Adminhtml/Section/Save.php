<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use MageWorx\Downloads\Api\Data\SectionLocaleInterfaceFactory;
use MageWorx\Downloads\Api\SectionRepositoryInterface;
use MageWorx\Downloads\Controller\Adminhtml\Section as SectionController;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\SectionFactory;

class Save extends SectionController
{
    /**
     * @var SectionLocaleInterfaceFactory
     */
    protected $sectionLocaleFactory;

    /**
     * Save constructor.
     *
     * @param SectionLocaleInterfaceFactory $sectionLocaleFactory
     * @param SectionRepositoryInterface $sectionRepository
     * @param Registry $registry
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        SectionLocaleInterfaceFactory $sectionLocaleFactory,
        SectionRepositoryInterface $sectionRepository,
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        parent::__construct($sectionRepository, $registry, $sectionFactory, $context);
        $this->sectionLocaleFactory = $sectionLocaleFactory;
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data           = $this->getRequest()->getPost('section');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data    = $this->filterData($data);
            $section = $this->initSection();
            $section->setData($data);
            $section->setStoreLocales($this->convertLocaleFormDataToObjects($data));

            $this->_eventManager->dispatch(
                'mageworx_downloads_section_prepare_save',
                [
                    'section' => $section,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $this->sectionRepository->save($section);
                $this->messageManager->addSuccessMessage(__('The section has been saved.'));
                $this->_getSession()->setMageWorxDownloadsSectionData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_downloads/*/edit',
                        [
                            'section_id' => $section->getId(),
                            '_current'   => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_downloads/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the section.'));
            }

            $this->_getSession()->setMageWorxDownloadsSectionData($data);
            $resultRedirect->setPath(
                'mageworx_downloads/*/edit',
                [
                    'section_id' => $section->getId(),
                    '_current'   => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('mageworx_downloads/*/');

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @param string|null $filename
     * @return \MageWorx\Downloads\Api\Data\SectionLocaleInterface[]
     */
    protected function convertLocaleFormDataToObjects(array $data, ?string $filename = null): array
    {
        $storeLabels = [];

        foreach ($data['store_section_names'] as $storeId => $name) {
            $storeLabelObj = $this->sectionLocaleFactory->create();

            $name = ((int)$storeId === 0 && !$name) ? $filename : $name;

            $storeLabelObj->setStoreId($storeId);
            $storeLabelObj->setStoreName($name);
            $storeLabelObj->setStoreDescription($data['store_section_descriptions'][$storeId]);
            $storeLabels[] = $storeLabelObj;
        }

        return $storeLabels;
    }
}
