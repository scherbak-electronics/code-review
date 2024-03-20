<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Model\Attachment;

use Magento\Framework\App\Filesystem\DirectoryList;

class Link extends \MageWorx\Downloads\Model\Attachment\Link
{
    const IMPORT_DIR = 'mageworx/downloads/import';

    /**
     * Get import dir
     *
     * @return string
     */
    public function getImportDir()
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(self::IMPORT_DIR);
    }

}