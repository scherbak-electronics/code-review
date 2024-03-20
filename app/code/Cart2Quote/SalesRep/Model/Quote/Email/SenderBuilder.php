<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email;

use Zend_Mail_Exception;

/**
 * Class SenderBuilder
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var \Cart2Quote\SalesRep\Helper\Data
     */
    private $salesRepHelper;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    private $userFactory;

    /**
     * @var \Cart2Quote\SalesRep\Model\ResourceModel\UserRepository
     */
    private $userRepository;

    /**
     * @var \Magento\Sales\Model\Order\Email\Container\Template
     */
    protected $templateContainer;

    /**
     * @var \Cart2Quote\SalesRep\Model\Quote\Email\Container\IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Cart2Quote\SalesRep\Model\Quote\Email\UploadTransportBuilder
     */
    protected $uploadTransportBuilder;

    /**
     * Sender resolver
     *
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * SenderBuilder constructor
     *
     * @param UploadTransportBuilder $uploadTransportBuilder
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Cart2Quote\SalesRep\Helper\Data $salesRepHelper
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\UserRepository $userRepository
     */
    public function __construct(
        \Cart2Quote\SalesRep\Model\Quote\Email\UploadTransportBuilder $uploadTransportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Cart2Quote\SalesRep\Helper\Data $salesRepHelper,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Cart2Quote\SalesRep\Model\ResourceModel\UserRepository $userRepository
    ) {
        if (class_exists(\Magento\Framework\Mail\Template\TransportBuilderByStore::class)) {
            parent::__construct(
                $templateContainer,
                $identityContainer,
                $transportBuilder,
                \Magento\Framework\App\ObjectManager::getInstance()->create(
                    \Magento\Framework\Mail\Template\TransportBuilderByStore::class
                )
            );
        } else {
            parent::__construct(
                $templateContainer,
                $identityContainer,
                $transportBuilder
            );
        }
        $this->salesRepHelper = $salesRepHelper;
        $this->userFactory = $userFactory;
        $this->moduleManager = $moduleManager;
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
        $this->uploadTransportBuilder = $uploadTransportBuilder;
        $this->senderResolver = $senderResolver;
        $this->userRepository = $userRepository;
    }

    /**
     * Prepare and send email message
     *
     * @param null $attachments
     * @param null $quote
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send(
        $attachments = null,
        $quote = null
    ) {
        $this->transportBuilder = $this->uploadTransportBuilder;
        $attachedPart = $this->attachFiles($attachments);
        $this->configureEmailTemplate();
        $emailAvailable = false;
        if ($quote && $quote->getProposalEmailReceiver()) {
            $emailAvailable = true;
            $this->transportBuilder->addTo(
                $quote->getProposalEmailReceiver()
            );
        } elseif ($this->identityContainer->getRecieverEmail()) {
            $emailAvailable = true;
            $this->transportBuilder->addTo(
                $this->identityContainer->getRecieverEmail(),
                $this->identityContainer->getRecieverName()
            );
        }
        if ($quote && $quote->getProposalEmailCc()) {
            $this->transportBuilder->addCc(
                explode(';', $quote->getProposalEmailCc())
            );
        }
        if (!$emailAvailable) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t send an email to a quote without an email address.')
            );
        }
        //Send copy to salesrep
        if ($this->identityContainer->isSendCopyToSalesRep() && $this->identityContainer->getCopyMethod() == 'bcc') {
            $emailIdentity = $this->senderResolver->resolve($this->identityContainer->getEmailIdentity());
            $this->transportBuilder->addBcc($emailIdentity['email']);
            try {
                if (isset($this->uploadTransportBuilder)) {
                    $this->uploadTransportBuilder->addBcc($emailIdentity['email']);
                }
            } catch (\Exception $exception) {
                //ignore
            }
        }
        $copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
                try {
                    if (isset($this->uploadTransportBuilder)) {
                        $this->uploadTransportBuilder->addBcc($email);
                    }
                } catch (\Exception $exception) {
                    //ignore
                }
            }
        }
        $transport = $this->uploadTransportBuilder->getMessage($attachedPart);
        $this->uploadTransportBuilder->resetUploadTransportBuilder();
        $transport->sendMessage();
    }

    /**
     * Prepare and send copy email message
     *
     * @param null $attachments
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendCopyTo(
        $attachments = null
    ) {
        //Send copy to salesrep
        if ($this->identityContainer->isSendCopyToSalesRep() && $this->identityContainer->getCopyMethod() == 'copy') {
            $this->configureEmailTemplate();
            $emailIdentity = $this->senderResolver->resolve($this->identityContainer->getEmailIdentity());
            $this->transportBuilder->addTo($emailIdentity['email']);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }

        //overwriten parent::sendCopyTo()
        $copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'copy') {
            $this->transportBuilder = $this->uploadTransportBuilder;
            $attachedPart = $this->attachFiles($attachments);
            $this->configureEmailTemplate();

            foreach ($copyTo as $email) {
                $this->transportBuilder->addTo($email);
                $transport = $this->transportBuilder->getMessage($attachedPart);
                $this->transportBuilder->resetUploadTransportBuilder();
                $transport->sendMessage();
            }
        }
    }

    /**
     * Attach files to email message
     *
     * @param $attachments
     * @return array
     */
    public function attachFiles($attachments)
    {
        $attachedPart = [];
        $isMagetrendEnabled = $this->moduleManager->isEnabled('Magetrend_PdfCart2Quote');

        if (is_array($attachments)) {
            foreach ($attachments as $attachmentName => $attachmentPath) {
                if (!file_exists($attachmentPath)) {
                    if ($isMagetrendEnabled) {
                        $attachmentPathParts = explode('//', $attachmentPath);
                        if (is_array($attachmentPathParts) && isset($attachmentPathParts[1])) {
                            $magetrendAttachmentPath = "/" . $attachmentPathParts[1];
                            if (file_exists($magetrendAttachmentPath)) {
                                $attachedPart[] = $this->transportBuilder->attachFile($magetrendAttachmentPath, $attachmentName);
                            }
                        }
                    }
                } else {
                    $attachedPart[] = $this->transportBuilder->attachFile($attachmentPath, $attachmentName);
                }
            }
        }

        return $attachedPart;
    }

    /**
     * Set the SalesRep as FROM if possible
     */
    protected function configureEmailTemplate()
    {
        if (!isset($this->salesRepHelper) || !isset($this->userFactory)) {
            parent::configureEmailTemplate();

            try {
                //setFromByScope only exists in the final fixed version of magento (>M2.3.0 and >M2.2.8))
                if (!method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'setFromByScope')) {
                    $this->transportBuilder->setFrom($this->identityContainer->getEmailIdentity());
                }
            } catch (Zend_Mail_Exception $exception) {
                //catch 'From Header set twice' error
                //That would mean that is Magento 2.1.x where this isn't an issue
            }
        } else {
            $isEmailSender = $this->salesRepHelper->isEmailSender();
            // can not set From field twice, it will crash and the email will not send
            $fromIsSet = false;
            if ($isEmailSender && isset($this->templateContainer->getTemplateVars()['quote'])) {
                $quote = $this->templateContainer->getTemplateVars()['quote'];
                $salesRepUserId = $quote->getUserId();
                if ($salesRepUserId === null) {
                    $salesRepUser = $this->userRepository->getMainUserByAssociatedId($quote->getQuoteId(), 'quotation');
                    $salesRepUserId = $salesRepUser->getUserId();
                }
                if (isset($salesRepUserId) && $salesRepUserId > 0) {
                    $salesRep = $this->userFactory->create()->load($salesRepUserId);
                    if ($salesRep) {
                        $sender = [
                            'email' => $salesRep->getEmail(),
                            'name' => $salesRep->getName()
                        ];
                        $this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
                        $this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
                        $this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
                        $this->transportBuilder->setFrom($sender);
                        $fromIsSet = true;
                    }
                }
            }

            if (!$fromIsSet) {
                parent::configureEmailTemplate();

                try {
                    //setFromByScope only exists in the final fixed version of magento (>M2.3.0 and >M2.2.8))
                    if (!method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'setFromByScope')) {
                        $this->transportBuilder->setFrom($this->identityContainer->getEmailIdentity());
                    }
                } catch (Zend_Mail_Exception $exception) {
                    //catch 'From Header set twice' error
                    //That would mean that is Magento 2.1.x where this isn't an issue
                }
            }
        }
    }

    /**
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     */
    public function getTransportBuilder()
    {
        return $this->transportBuilder;
    }

    /**
     * @return \Cart2Quote\SalesRep\Model\Quote\Email\UploadTransportBuilder
     */
    public function getUploadTransportBuilder()
    {
        return $this->uploadTransportBuilder;
    }

    /**
     * @return \Cart2Quote\SalesRep\Model\Quote\Email\Container\IdentityInterface
     */
    public function getIdentityContainer()
    {
        return $this->identityContainer;
    }

    /**
     * @return \Magento\Sales\Model\Order\Email\Container\Template
     */
    public function getTemplateContainer()
    {
        return $this->templateContainer;
    }
}
