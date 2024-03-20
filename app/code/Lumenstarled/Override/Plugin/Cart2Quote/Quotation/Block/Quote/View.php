<?php
namespace Lumenstarled\Override\Plugin\Cart2Quote\Quotation\Block\Quote;
class View {
	public function __construct(
		\Lumenstarled\Override\Helper\StoreManager $storeManager,
		\Cart2Quote\Quotation\Helper\Data $quotationHelper
	){
		$this->storeManager = $storeManager;
		$this->quotationHelper = $quotationHelper;
	}
	public function afterGetAcceptUrl(
		\Cart2Quote\Quotation\Block\Quote\View $subject,
		$result
	){
		$quote = $subject->getQuote();
		$quoteStoreId = $quote->getStore()->getId();
		$currentStoreId = $this->storeManager->getStoreId();
		if( $quoteStoreId <> $currentStoreId ){
			$acceptWithoutCheckout = $this->isCheckoutDisabled();
			if ($acceptWithoutCheckout) {
				return $this->storeManager->getUrl(
					$quoteStoreId,
					'quotation/quote_checkout/acceptwithoutcheckout',
					['quote_id' => $quote->getId()]
				);
			}
			
			return $this->storeManager->getUrl(
				$quoteStoreId,
				'quotation/quote_checkout/accept',
				['quote_id' => $quote->getId()]
			);
		}
		
		return $result;
    }
	
	protected function isCheckoutDisabled(){
        return $this->quotationHelper->isCheckoutDisabled();
    }
}