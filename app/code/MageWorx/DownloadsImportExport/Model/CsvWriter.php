<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class CsvWriter
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * CsvWriter constructor.
     *
     * @param Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function write(array $data): array
    {
        $filename = 'export/' . hash('md5', microtime()) . '.csv';
        $stream   = $this->directory->openFile($filename, 'a+');
        $stream->lock();

        foreach ($data as $datum) {
            $stream->writeCsv($datum);
        }

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $filename,
            'rm'    => true  // can delete file after use
        ];
    }
}
