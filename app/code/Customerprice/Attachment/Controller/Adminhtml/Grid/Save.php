<?php

namespace Customerprice\Attachment\Controller\Adminhtml\Grid;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    var $gridFactory;

    protected $resultJsonFactory;
    protected $regionCollectionFactory;
    protected $directoryList;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Customerprice\Attachment\Model\GridFactory $gridFactory,
        JsonFactory $resultJsonFactory,
        RegionCollectionFactory $regionCollectionFactory,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->directoryList = $directoryList;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $pdfFile = $_FILES['file']["name"];
        $allowedFormats = ['pdf', 'xls', 'xlsx', 'xlsm', 'xlsb', 'xlam'];
        $maxFileSize = 10 * 1024 * 1024; // 10 MB...
        $fileSize = $_FILES['file']['size'];
        $fileExtension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedFormats) && ($fileSize <= $maxFileSize)) 
        {
            $this->messageManager->addError(__('Please upload these [.pdf, .xls, .xlsx, .xlsm, .xlsb, .xlam] format file only and file size must be equal or less than 10 MB'));
        }
        else {
            $targetDirectory = $this->directoryList->getPath('media');
            $targetFilePath = $targetDirectory . '/customerImage/' . basename($pdfFile);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath))
            {
                echo "";
            }
              
            if (!$data) {
                $this->_redirect('grid/grid/addrow');
                return;
            }
            try {
                $rowData = $this->gridFactory->create();
                $rowData->setData($data);
                $rowData->setData('file', $pdfFile);

                if (isset($data['id'])) {
                    $rowData->setEntityId($data['id']);
                }
                $rowData->save();
                $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }
        }
        $this->_redirect('grid/grid/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Customerprice_Attachment::save');
    }
}






