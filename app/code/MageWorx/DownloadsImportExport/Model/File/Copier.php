<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Model\File;

class Copier extends \Magento\Framework\File\Uploader
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Init upload
     *
     * @param string|array $fileId
     * @throws \Exception
     */
    public function __construct($fileId, $data)
    {
        $this->setFilesSimulatorData($data);
        $this->_setUploadFileId($fileId);
        if (!file_exists($this->_file['tmp_name'])) {

            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested file is not found: %1.',
                    htmlspecialchars($this->_file['tmp_name'])
                )
            );

            $code = empty($this->_file['tmp_name']) ? self::TMP_NAME_EMPTY : 0;
            throw new \Exception('The file was not uploaded.', $code);
        }
        else {
            $this->_fileExists = true;
        }
    }

    /**
     * Validates destination directory to be writable
     *
     * @param string $destinationFolder
     * @return void
     * @throws \Exception
     */
    private function validateDestination($destinationFolder)
    {
        if ($this->_allowCreateFolders) {
            $this->_createDestinationFolder($destinationFolder);
        }

        if (!is_writable($destinationFolder)) {
            throw new \Exception('Destination folder is not writable or does not exists.');
        }
    }

    /**
     * Return file mime type
     *
     * @return string
     */
    private function _getMimeType()
    {
        return $this->_file['type'];
    }

    /**
     * @param array $data
     */
    protected function setFilesSimulatorData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    protected function getFilesSimulatorData()
    {
        return $this->data;
    }

    /**
     * Set upload field id
     *
     * @param string|array $fileId
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function _setUploadFileId($fileId)
    {
        if (is_array($fileId)) {
            $this->_uploadType = self::MULTIPLE_STYLE;
            $this->_file = $fileId;
        }
        else {
            $files = $this->getFilesSimulatorData();

            if (empty($files)) {
                throw new \Exception("File's array is empty");
            }

            preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

            if (is_array($file) && count($file) > 0 && count($file[0]) > 0 && count($file[1]) > 0) {
                array_shift($file);
                $this->_uploadType = self::MULTIPLE_STYLE;

                $fileAttributes = $files[$file[0]];
                $tmpVar = [];

                foreach ($fileAttributes as $attributeName => $attributeValue) {
                    $tmpVar[$attributeName] = $attributeValue[$file[1]];
                }

                $fileAttributes = $tmpVar;
                $this->_file = $fileAttributes;
            }
            elseif (!empty($fileId) && isset($files[$fileId])) {
                $this->_uploadType = self::SINGLE_STYLE;
                $this->_file = $files[$fileId];
            }
            elseif ($fileId == '') {
                throw new \Exception('Invalid parameter given. A valid $_FILES[] identifier is expected.');
            }
        }
    }

    /**
     * Create destination folder
     *
     * @param string $destinationFolder
     * @return \Magento\Framework\File\Uploader
     * @throws \Exception
     */
    private function _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == '/') {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(is_dir($destinationFolder)
            || mkdir($destinationFolder, 0777, true)
        )) {
            throw new \Exception("Unable to create directory '{$destinationFolder}'.");
        }

        return $this;
    }
}
