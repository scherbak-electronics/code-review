<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Controller\Adminhtml;

use MageWorx\Downloads\Api\SectionRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Registry;


abstract class Section extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_Downloads::sections';

    /**
     * Section factory
     *
     * @var SectionFactory
     */
    protected $sectionFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var SectionRepositoryInterface
     */
    protected $sectionRepository;

    /**
     * Section constructor.
     *
     * @param SectionRepositoryInterface $sectionRepository
     * @param Registry $registry
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        SectionRepositoryInterface $sectionRepository,
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->coreRegistry          = $registry;
        $this->sectionFactory        = $sectionFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->sectionRepository     = $sectionRepository;
    }

    /**
     * @return \MageWorx\Downloads\Model\Section
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initSection()
    {
        $sectionId = $this->getRequest()->getParam('section_id');

        if ($sectionId) {
            $section = $this->sectionRepository->get($sectionId);
        } else {
            $section = $this->sectionFactory->create();
        }

        $this->coreRegistry->register('mageworx_downloads_section', $section);

        return $section;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        return $data;
    }
}
