<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email;

use Magento\Email\Model\AbstractTemplate;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;

/**
 * Class UploadTransportBuilder
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email
 */
class UploadTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @var \Cart2Quote\SalesRep\Model\Email\ZendAdapter
     */
    private $zendAdapter;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    private $messageData = [];

    /**
     * Template data
     *
     * @var array
     */
    protected $templateData = [];

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * @var AddressConverter|null
     */
    private $addressConverter;

    /**
     * UploadTransportBuilder constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Mail\Template\FactoryInterface $templateFactory
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory
     * @param \Magento\Framework\Mail\MessageInterfaceFactory|null $messageFactory
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageInterfaceFactory $messageFactory = null
    ) {
        if (method_exists(\Magento\Framework\Mail\Message::class, 'addAttachment')) {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\SalesRep\Model\Email\ZendOne::class);
            $this->productMetadata = $productMetadata;
        } elseif (class_exists('Magento\Framework\Mail\MimeMessage')) {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory,
                $messageFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\SalesRep\Model\Email\ZendTree::class);
            $this->emailMessageInterfaceFactory = $objectManager->get(EmailMessageInterfaceFactory::class);
            $this->mimeMessageInterfaceFactory = $objectManager->get(MimeMessageInterfaceFactory::class);
            $this->mimePartInterfaceFactory = $objectManager->get(MimePartInterfaceFactory::class);
            $this->addressConverter = $objectManager->get(AddressConverter::class);
        } else {
            parent::__construct(
                $templateFactory,
                $message,
                $senderResolver,
                $objectManager,
                $mailTransportFactory,
                $messageFactory
            );

            $this->zendAdapter = $objectManager->get(\Cart2Quote\SalesRep\Model\Email\ZendTwo::class);
        }
    }

    /**
     * Add cc address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     * @throws MailException
     */
    public function addCc($address, $name = '')
    {
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('cc', $address, $name);
        } else {
            parent::addCc($address, $name);
        }

        return $this;
    }

    /**
     * Add to address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return $this
     * @throws MailException
     */
    public function addTo($address, $name = '')
    {
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('to', $address, $name);
        } else {
            parent::addTo($address, $name);
        }
        return $this;
    }

    /**
     * Add bcc address
     *
     * @param array|string $address
     *
     * @return $this
     * @throws MailException
     */
    public function addBcc($address)
    {
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('bcc', $address);
        } else {
            parent::addBcc($address);
        }

        return $this;
    }

    /**
     * Set Reply-To Header
     *
     * @param string $email
     * @param string|null $name
     *
     * @return $this
     * @throws MailException
     */
    public function setReplyTo($email, $name = null)
    {
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('replyTo', $email, $name);
        } else {
            parent::setReplyTo($email, $name);
        }

        return $this;
    }

    /**
     * Set mail from address
     *
     * @param string|array $from
     *
     * @return $this
     * @throws MailException
     * @see setFromByScope()
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from);
    }

    /**
     * Set mail from address by scopeId
     *
     * @param string|array $from
     * @param string|int $scopeId
     *
     * @return $this
     * @throws MailException
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            //M2.3.3 support
            $this->addAddressByType('from', $result['email'], $result['name']);
        } else {
            if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'setFromByScope')) {
                //M2.3.1 support (and some M2.2.x versions)
                parent::setFromByScope($from, $scopeId);
            } else {
                //M2.1 support
                $this->message->setFrom($result['email'], $result['name']);
            }
        }

        return $this;
    }

    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return array
     */
    public function attachFile($file, $name)
    {
        return $this->zendAdapter->attachFileAdapter($file, $name);
    }

    /**
     * Get mail message
     *
     * @param array $attachedPart
     * @return \Magento\Framework\Mail\TransportInterface
     */
    public function getMessage($attachedPart)
    {
        $template = $this->getTemplate();
        $body = $template->processTemplate();

        if (class_exists('Magento\Framework\Mail\MimeMessage')) {
            $this->messageData['subject'] = html_entity_decode($template->getSubject(), ENT_QUOTES);

            return $this->prepareQuoteMessage($attachedPart, $body);
        }

        $this->zendAdapter->getMessageAdapter($attachedPart, $body, $this->message);
        $this->message->setSubject(html_entity_decode($template->getSubject(), ENT_QUOTES));

        return $this->mailTransportFactory->create(['message' => clone $this->message]);
    }

    /**
     * Reset UploadTransportBuilder object state
     */
    public function resetUploadTransportBuilder()
    {
        $this->reset();
    }

    /**
     * Sets up template filter
     *
     * @param AbstractTemplate $template
     *
     * @return void
     */
    protected function setTemplateFilter(AbstractTemplate $template)
    {
        if (isset($this->templateData['template_filter'])) {
            $template->setTemplateFilter($this->templateData['template_filter']);
        }
    }

    /**
     * @param array $attachedPart
     * @param string $body
     * @return \Magento\Framework\Mail\TransportInterface
     */
    protected function prepareQuoteMessage($attachedPart, $body)
    {
        $mimePart[] = $this->mimePartInterfaceFactory->create(
            ['content' => $body]
        );

        foreach ($attachedPart as $part) {
            $mimePart[] = $part;

            //set charset based on attached parts
            try {
                $this->messageData['encoding'] = $part->getCharset();
            } catch (\Exception $exception) {
                //do nothing
            }
        }

        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $mimePart]
        );
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);

        return $this->mailTransportFactory->create(['message' => clone $this->message]);
    }

    /**
     * Handles possible incoming types of email (string or array)
     * Note: addressConverter is only set when on Magento 2.3.3+
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws MailException
     */
    private function addAddressByType(string $addressType, $email, $name = null)
    {
        if (is_array($email)) {
            if (isset($this->messageData[$addressType])) {
                $this->messageData[$addressType] = array_merge(
                    $this->messageData[$addressType],
                    $this->addressConverter->convertMany($email)
                );
            } else {
                $this->messageData[$addressType] = $this->addressConverter->convertMany($email);
            }
            return;
        }
        $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
    }

    /**
     * Reset object state
     *
     * @return $this|\Magento\Framework\Mail\Template\TransportBuilder
     */
    protected function reset()
    {
        $this->messageData = [];
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;

        return parent::reset();
    }
}
