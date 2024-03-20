<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\File;

use MageWorx\Downloads\Api\Data\File\ContentInterface;
use Magento\Framework\Exception\InputException;

class ContentValidator
{
    /**
     * Check if gallery entry content is valid
     *
     * @param ContentInterface $fileContent
     * @return bool
     * @throws InputException
     */
    public function isValid(ContentInterface $fileContent)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $decodedContent = base64_decode($fileContent->getFileData(), true);
        // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
        if (empty($decodedContent)) {
            throw new InputException(__('Provided content must be valid base64 encoded data.'));
        }

        if (!$this->isFileNameValid($fileContent->getName())) {
            throw new InputException(__('Provided file name contains forbidden characters.'));
        }

        return true;
    }

    /**
     * Check if given filename is valid
     *
     * @param string $fileName
     * @return bool
     */
    protected function isFileNameValid($fileName)
    {
        // Cannot contain \ / : * ? " < > |
        if (!preg_match('/^[^\\/?*:";<>()|{}\\\\]+$/', $fileName)) {
            return false;
        }

        return true;
    }
}
