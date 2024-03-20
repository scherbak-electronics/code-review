<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Plugin;

use Cart2Quote\SalesRep\Model\Quote\Email\Container\SalesRepIdentityInterface;

/**
 * Class SenderBuilder
 *
 * @package Cart2Quote\SalesRep\Plugin
 */
class SenderBuilder
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \Cart2Quote\SalesRep\Model\User
     */
    protected $userRepository;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * SenderBuilder constructor
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\UserRepository $userRepository
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Cart2Quote\SalesRep\Model\ResourceModel\UserRepository $userRepository,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    /**
     * Prepare and send email message
     *
     * @param \Cart2Quote\SalesRep\Model\Quote\Email\SenderBuilder $subject
     * @param array|null $attachments
     * @param \Cart2Quote\Quotation\Model\Quote|null $quote
     */
    public function beforeSend(\Cart2Quote\SalesRep\Model\Quote\Email\SenderBuilder $subject, $attachments, $quote)
    {
        // get email identity
        $identityContainer = $subject->getIdentityContainer();

        // check if identity is instance of \Cart2Quote\SalesRep\Model\Quote\Email\Container\SalesRepIdentityInterface
        if ($identityContainer instanceof SalesRepIdentityInterface) {
            $isSendCopyToAssignedSalesRep = $identityContainer->isSendCopyToAssignedSalesRep();
            $isSalesRepSender = $identityContainer->getSender();

            if ($isSendCopyToAssignedSalesRep || $isSalesRepSender) {
                $quote = $subject->getTemplateContainer()->getTemplateVars()['quote'];
                $salesRepUserId = $quote->getUserId();
                if ($salesRepUserId === null) {
                    $salesRepUser = $this->userRepository->getMainUserByAssociatedId($quote->getQuoteId(), 'quotation');
                    $salesRepUserId = $salesRepUser->getUserId();
                }
                if (isset($salesRepUserId)) {
                    $salesRep = $this->userFactory->create()->load($salesRepUserId);
                    $salesRepEmail = $salesRep->getEmail();

                    if (isset($salesRepEmail)) {
                        $subject->getTransportBuilder()->addBcc($salesRepEmail);

                        try {
                            if ($subject->getUploadTransportBuilder()) {
                                $subject->getUploadTransportBuilder()->addBcc($salesRepEmail);
                            }
                        } catch (\Exception $exception) {
                            //ignore
                        }
                    }
                }
            }
        }

        return [$attachments, $quote];
    }
}
