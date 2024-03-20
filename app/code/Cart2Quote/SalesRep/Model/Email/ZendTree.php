<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Email;

use Magento\Framework\Mail\MimePartInterfaceFactory;

/**
 * Class ZendTree
 *
 * @package Cart2Quote\SalesRep\Model\Email
 */
class ZendTree extends ZendAdapter
{
    /**
     * @var \Magento\Framework\Mail\MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    /**
     * ZendTree constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->mimePartInterfaceFactory = $objectManager->get(MimePartInterfaceFactory::class);
    }

    /**
     * Get attach file adapter
     *
     * @param string $file
     * @param string $name
     * @return \Zend\Mime\Part
     */
    public function attachFileAdapter($file, $name)
    {
        if (!empty($file) && file_exists($file)) {
            $fileContents = fopen($file, 'r');
            $attachment = $this->mimePartInterfaceFactory->create(
                [
                    'content' => $fileContents,
                    'type' => \Zend\Mime\Mime::TYPE_OCTETSTREAM,
                    'fileName' => $name,
                    'disposition' => \Zend\Mime\Mime::DISPOSITION_ATTACHMENT,
                    'encoding' => \Zend\Mime\Mime::ENCODING_BASE64
                ]
            );

            return $attachment;
        }
    }

    /**
     * Get message adapter
     * Adapter not needed after Magento 2.3.3 and higher
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {
    }
}
