<?php
namespace Lumenstarled\Override\Observer;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class ConvertQuote implements ObserverInterface {
	protected $_storeManager;
	protected $_responseFactory;
    protected $_url;
    protected $_customerSession;
	protected $_checkoutSession;
    protected $_objectManager;
    protected $_request;
	protected $_quoteFactory;
	protected $_cart;
	protected $_cookie;
	protected $_logger;
	static $_wsCA = 1;
    static $_wsUS = 3;
	
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\Request\Http $request,
		\Magento\Quote\Model\QuoteRepository $quoteFactory,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Cart2Quote\Quotation\Model\Session $quotationSession,
		\Cart2Quote\Quotation\Controller\Quote\Add $addQuote,
		\Cart2Quote\Quotation\Model\Quote $quotationQuote,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Locale\ResolverInterface $resolverInterface,
		\Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
		\Magento\Catalog\Helper\Product $productHelper,
		\Cart2Quote\Quotation\Model\QuotationCart $quotationCart,
		\Magento\Framework\Event\Manager $eventManager,
		\Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider,
		
		\Magento\Framework\Escaper $escaper, 
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		#\Magento\Framework\App\RequestInterface $request,
		\Lumenstarled\Override\Helper\Cookie $cookie,
		\Lumenstarled\Override\Helper\Logger $_logger,
		\Psr\Log\LoggerInterface $logger
    ) {
		$this->_storeManager = $storeManager;
		$this->_customerSession = $customerSession;
		$this->_checkoutSession = $checkoutSession;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_objectManager = $objectmanager;
        $this->_request = $request;
		$this->_quoteFactory = $quoteFactory;
		$this->_cart = $cart;
		$this->_productFactory = $productFactory;
		$this->_quotationSession = $quotationSession;
		$this->_addQuote = $addQuote;
		$this->_quotationQuote = $quotationQuote;
		$this->messageManager = $messageManager;
		$this->resolverInterface = $resolverInterface;
		$this->quotationDataHelper = $quotationDataHelper;
		$this->productHelper = $productHelper;
		$this->quotationCart = $quotationCart;
		$this->_eventManager = $eventManager;
		$this->strategyProvider = $strategyProvider;
		$this->escaper = $escaper;
		$this->productRepository = $productRepository;
		#$this->_request = $request;
		$this->_cookie = $cookie;
		$this->_logger = $_logger;
		$this->logger = $logger;
    }
	public function execute(Observer $observer) {
		$customer = $this->_customerSession->getCustomer();
		$updated = $this->_cookie->getCookie('t_quote_updated');
		if( $this->_customerSession->isLoggedIn() && !$updated){
			#$currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
			$changeFromWsId = $this->_cookie->getCookie('t_change_from_wsid');
			$fromQuoteId = $this->_cookie->getCookie('t_quote_wsid_' . $changeFromWsId . '_' . 'customer_id_' . $customer->getId());
			if($fromQuoteId){
				#$curQuote = $this->_checkoutSession->getQuote();
				#$curQuoteitems = $curQuote->getAllVisibleItems();
				
				$fromQuote = $this->_quoteFactory->get($fromQuoteId);
                $fromQuoteitems = $fromQuote->getAllVisibleItems();
				/* $logfile = 't-debug.log';
				$this->_logger->addlog('Begin....', $logfile);
				$this->_logger->addlog("CurrentWS:{$currentWebsiteId} @ FromWS:{$changeFromWsId}", $logfile);
				$this->_logger->addlog("CurrentQuote:{$curQuote->getId()} @ FromQuote:{$fromQuote->getId()}", $logfile);
				$this->_logger->addlog("CurrentItems:" . count($curQuoteitems) . " @ FromItems:" . count($fromQuoteitems), $logfile);
				$this->_logger->addlog("Quotation:" . $this->_quotationSession->getQuote()->getId(), $logfile); */

				if( count($fromQuoteitems) ){
					$this->_quotationSession->clearQuote();
					foreach ($fromQuoteitems as $item) {
						$this->addToQuote($item);
					}
				}	
				$this->_cookie->setPublicCookie('t_quote_updated', 1);
			}
		}
	}
	
	public function addToQuote($item){
		$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
		$params = $options['info_buyRequest'];
        try {
			
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    [
                        'locale' => $this->resolverInterface->getLocale()
                    ]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
			
			$productId = $item->getProduct()->getId();
            $product = $this->_productFactory->create()->load($productId);
            $related = @$params['related_product'];

            if (!$this->quotationDataHelper->isStockEnabledFrontend()) {
                $this->productHelper->setSkipSaleableCheck(true);
                $this->quotationCart->getQuote()->setIsSuperMode(true);
            }

            $this->quotationCart->addProduct($product, $params);
			
            if (!empty($related)) {
                $this->quotationCart->addProductsByIds(explode(',', $related));
            }

            $this->quotationCart->getQuote()->setIsQuotationQuote(true);
            $this->quotationCart->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_quotationSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }
           
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add this item to your quote right now.'));
            $this->logger->critical($e);
        }
    }

	public function clearCurrentQuote(){
		$quoteItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
		foreach($quoteItems as $item){
			$this->_cart->removeItem($item->getId())->save(); 
		}
	}
}