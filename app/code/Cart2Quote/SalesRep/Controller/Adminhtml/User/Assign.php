<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Controller\Adminhtml\User;

use Cart2Quote\SalesRep\Api\Data\UserInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\TestFramework\Inspection\Exception;

/**
 * Class Assign
 * @package Cart2Quote\SalesRep\Controller\Adminhtml\User
 */
class Assign extends \Magento\Backend\App\Action
{
    const SALESREP_ADMIN_USER = 'salesrep_user';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * User Repository
     *
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * User Factory
     *
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory
     */
    protected $userFactory;

    /**
     * Assign constructor.
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Get admin users drop down
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $layout = $this->_view->getLayout();

        try {
            $resultJson = $this->resultJsonFactory->create();
            $objectId = $this->getRequest()->getParam('id');
            $typeId = $this->getRequest()->getParam('type_id');
            $userId = $this->getRequest()->getParam('user_id');
            $stickUser = $this->getRequest()->getParam('stick_user');
            $customerId = $this->getRequest()->getParam('customer_id');
            if ($userId === "") {
                $response = [
                    'html' => __('No salesrep selected'),
                    'success' => false
                ];

                return $resultJson->setHttpResponseCode(417)->setData($response);
            }

            //remove salesrep?

            if ($userId === "0") {
                $currentUser = $this->userRepository->getMainUserByAssociatedId($objectId, $typeId);
                if ($currentUser->getId()) {
                    $this->userRepository->delete($currentUser);
                } else {
                    $response = $this->getFailedReturn();
                }

                $salesRep = $this->userFactory->create();
            } else {
                $user = $this->userFactory->create();
                $user->setIsMain(true);
                $user->setObjectId($objectId);
                $user->setTypeId($typeId);
                $user->setUserId($userId);

                $salesRep = $this->userRepository->save($user);
                
                if ($stickUser) {
                    $this->setAsDefault($customerId, $userId);
                }
            }

            /** @var \Cart2Quote\SalesRep\Block\Adminhtml\SalesRep\User $block */
            $block = $layout
                ->createBlock(
                    \Cart2Quote\SalesRep\Block\Adminhtml\SalesRep\User::class,
                    self::SALESREP_ADMIN_USER,
                    [
                        'data' => []
                    ]
                )
                ->setTemplate('Cart2Quote_SalesRep::user.phtml');
            if (!$block instanceof \Cart2Quote\SalesRep\Block\Adminhtml\SalesRep\User) {
                throw new \Exception(
                    __('Block need to be instance of \Cart2Quote\SalesRep\Block\Adminhtml\SalesRep\User')
                );
            }
            $html = $block->setSalesRep($salesRep)->toHtml();
            if ($html) {
                $response = [
                    'html' => $html,
                    'success' => true
                ];
            } else {
                $response = $this->getFailedReturn();
            }
        } catch (\Exception $e) {
            $response = $this->getFailedReturn();
        }

        return $resultJson->setHttpResponseCode(200)->setData($response);
    }

    /**
     * Get failed return message
     *
     * @return array
     */
    private function getFailedReturn()
    {
        return $response = [
                'html' => __('Error while saving the Sales Representative'),
                'success' => false
            ];
    }

    /**
     * @param int $objectId
     * @param int $adminId
     */
    protected function setAsDefault($objectId, $adminId)
    {
        $user = $this->userRepository->getMainUserByAssociatedId(
            $objectId,
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER
        );

        if ($user->getId()) {
            $user->setUserId($adminId);
        } else {
            $user->setIsMain(true);
            $user->setObjectId($objectId);
            $user->setTypeId(\Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER);
            $user->setUserId($adminId);
        }

        $this->userRepository->save($user);
    }
}
