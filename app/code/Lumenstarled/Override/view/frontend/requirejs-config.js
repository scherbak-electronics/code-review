var config = {
    map: {
        '*': {
			'Cart2Quote_Quotation/template/quote-checkout/view/add-to-quote-button.html':               'Lumenstarled_Override/template/quote-checkout/view/add-to-quote-button.html',
			'Cart2Quote_Quotation/js/quote-checkout/action/place-quote':'Lumenstarled_Override/js/quote-checkout/action/place-quote'
        }
	},
    config: {
        mixins: {
			'Cart2Quote_Quotation/js/quote-checkout/view/add-to-quote-button': {
				'Lumenstarled_Override/js/quote-checkout/view/add-to-quote-button-mixin': true
			},		
			 'Magento_ReCaptchaFrontendUi/js/reCaptcha': {
                'Lumenstarled_Override/js/recaptcha-mixin': true
            },
			'Magento_ConfigurableProduct/js/configurable': {
                'Lumenstarled_Override/js/mixin/configurable': true
            }
		}
    }
}