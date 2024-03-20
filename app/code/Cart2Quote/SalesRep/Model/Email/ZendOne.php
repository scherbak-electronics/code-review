<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Email;

/**
 * Class ZendOne
 *
 * @package Cart2Quote\SalesRep\Model\Email
 */
class ZendOne extends \Cart2Quote\SalesRep\Model\Email\ZendAdapter
{
    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return \Zend_Mime_Part
     */
    public function attachFileAdapter($file, $name)
    {
        if (!empty($file) && file_exists($file)) {
            $fileContents = fopen($file, 'r');
            $attachment = new \Zend_Mime_Part($fileContents);
            $attachment->type = \Zend_Mime::TYPE_OCTETSTREAM;
            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
            $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->filename = $name;

            return $attachment;
        }
    }

    /**
     * Get message adapter
     *
     * @param array $attachedPart
     * @param string $body
     * @param null|\Magento\Framework\Mail\Message $message
     */
    public function getMessageAdapter($attachedPart, $body, $message = null)
    {
        $message->setMessageType(\Zend_Mime::TYPE_HTML);
        $message->setBody($body);
        if (!empty($attachedPart)) {
            foreach ($attachedPart as $part) {
                $message->addAttachment($part);
            }
        }
    }
}
