<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Email;

/**
 * Class ZendTwo
 *
 * @package Cart2Quote\SalesRep\Model\Email
 */
class ZendTwo extends ZendAdapter
{
    /**
     * Get attach file adpater
     *
     * @param string $file
     * @param string $name
     * @return \Zend\Mime\Part
     */
    public function attachFileAdapter($file, $name)
    {
        if (!empty($file) && file_exists($file)) {
            $fileContents = fopen($file, 'r');
            $attachment = new \Zend\Mime\Part($fileContents);
            $attachment->type = \Zend\Mime\Mime::TYPE_OCTETSTREAM;
            $attachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
            $attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
            $attachment->filename = $name;

            return $attachment;
        }
    }

    /**
     * Get message adapter
     *
     * @param array $attachedPart
     * @param string $body
     * @param \Magento\Framework\Mail\Message|\Zend\Mime\Message|null $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {
        /** @var \Zend\Mime\Message $mimeMessage */
        $mimeMessage = new \Zend\Mime\Message();
        $mimePart = new \Zend\Mime\Part($body);
        $mimePart->type = \Zend\Mime\Mime::TYPE_HTML;
        $mimePart->charset = 'utf-8';

        $mimeMessage->setParts([$mimePart]);
        if (!empty($attachedPart)) {
            foreach ($attachedPart as $part) {
                $mimeMessage->addPart($part);
            }
        }
        $message->setBody($mimeMessage);
    }
}
