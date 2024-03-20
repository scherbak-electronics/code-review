<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskEmail\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Cart2Quote\DeskEmail\Model\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;

/**
 * Class SenderBuilder
 * @package Cart2Quote\DeskEmail\Model
 */
class SenderBuilder
{
    /**
     * Email template
     *
     * @var Template
     */
    protected $templateContainer;

    /**
     * Email Identity Container
     *
     * @var IdentityInterface
     */
    protected $identityContainer;

    /**
     * Transport builder
     *
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Class SenderBuilder constructor
     *
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder
    ) {
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Prepare and send email message
     *
     * @return void
     */
    public function send()
    {
        $this->configureEmailTemplate();

        $this->transportBuilder->addTo(
            $this->identityContainer->getMainEmail(),
            $this->identityContainer->getMainName()
        );

        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        //send copy (non cc/bcc)
        try {
            $this->sendCopyTo();
        } catch (\Exception $e) {
            //do nothing
        }
    }

    /**
     * Prepare and send copy email message
     *
     * @return $this
     */
    public function sendCopyTo()
    {
        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'copy') {
            foreach ($copyTo as $email) {
                $this->configureEmailTemplate();

                $this->transportBuilder->addTo($email);

                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();
            }
        }
        return $this;
    }

    /**
     * Configure email template
     *
     * @return void
     */
    protected function configureEmailTemplate()
    {
        $this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
        $this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
        $this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
        $this->transportBuilder->setFrom($this->identityContainer->getEmailIdentity());
    }
}
