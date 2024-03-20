<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Model;

use MageWorx\DownloadsImportExport\Model\File\Copier;

class Copy
{
    /**
     * Uploader factory
     *
     * @var \MageWorx\DownloadsImportExport\Model\File\CopierFactory
     */
    protected $copierFactory;

    /**
     * @var array|null
     */
    protected $fileDataAfterCopy = null;

    /**
     * Upload constructor.
     *
     * @param File\CopierFactory $copierFactory
     */
    public function __construct(\MageWorx\DownloadsImportExport\Model\File\CopierFactory $copierFactory)
    {
        $this->copierFactory = $copierFactory;
    }

    /**
     * @param string $destinationFolder
     * @param array $data
     * @return array|string
     * @throws \Exception
     */
    public function copyFileAndGetName($destinationFolder, $data)
    {
        /** @var \MageWorx\DownloadsImportExport\Model\File\Copier $copier */
        $copier = $this->copierFactory->create(['fileId' => 'multifile', 'data' => $data]);
        $copier->setAllowRenameFiles(true);
        $copier->setFilesDispersion(true);
        $copier->setAllowCreateFolders(true);

        $result = $copier->save($destinationFolder);
        $this->fileDataAfterCopy = $result;

        return $result;
    }

    /**
     * @return array|null
     */
    public function getFileDataAfterUpload()
    {
        return $this->fileDataAfterCopy;
    }
}
