define(
    [
        'jquery',
		'Magento_Ui/js/modal/modal',
		'Magento_Checkout/js/model/quote',
		'Magento_Customer/js/model/customer',
		'mage/url',
		
		'mage/cookies',
		'mage/mage',
		'jquery/ui',
		'jquery/validate'
		
    ], function ($, modal,  quote, customer, url) {
        'use strict';

        var mixin = {
			hasDefaultAddress: function(){
				var addresses = customer.customerData.addresses;
				var hasBillingAddress = false;
				var hasShippingAddress = false;
				if(addresses &&Object.keys(addresses).length > 0 ){
					$.each(addresses, function(index, address){
						//console.log('---------------------------------------');
						$.each(address, function(key, val){
							if( key=='id' || key == 'default_billing' || key =='default_shipping' ){
								if( key == 'default_billing' && val ) hasBillingAddress = true;
								if( key == 'default_shipping' && val ) hasShippingAddress = true;
							}
						});
					});
				}
				
				return hasBillingAddress && hasShippingAddress;
			},
            validateQuote: function () {
				if( !this.hasDefaultAddress() ){
					//console.log('no.....address set default.');
					 
					/* confirmation({
						title: '',
						content: 'You have not set shipping and billing addresses, please update before submitting quote.',
						actions: {
							confirm: function(){
								var urlRedirect = url.build('customer/address');
								window.location.href = urlRedirect;
							},
							cancel: function(){
								return false;
							},
							always: function(){
								return false;
							}
						}
					
					}); */
					var r = confirm("You have not set shipping and billing addresses, please update before submitting quote.");
					if (r == true) {
						var urlRedirect = url.build('customer/address');
						window.location.href = urlRedirect;
					}
					return false;
				}else{
					//console.log('yes....address set default');
				}
				
				if( $('#co-add-to-quote-form').valid() ){
					window.checkoutConfig.company_cus = $('input[name=company]').val();
					this._super();
				}else{
					return false;
				}
            },
			setDefaultCurrency: function(){
				//console.log('quote : ' + window.checkoutConfig.quoteData.entity_id);
				var websiteId = window.checkout.websiteId;
				if(websiteId == 1){
					$('select[name=quote_currency_code]').val('CAD');
				}else{
					$('select[name=quote_currency_code]').val('USD');
				}
			},
			changeCurrency:function (data, event) {
				var domain = 'lumenstarled.com';
				var quote_id = window.checkoutConfig.quoteData.entity_id;
                var websiteId = window.checkout.websiteId;
				var currentUrl = $(location).attr('href');
				var customer_id = customer.customerData.id;
				var quoteName = 't_quote_wsid_' + websiteId + '_' + 'customer_id_' + customer_id;
				
				$.cookie(quoteName, quote_id, { path: '/', domain: domain } );
				$.cookie('t_change_from_wsid', websiteId, { path: '/', domain: domain });
				$.cookie('t_quote_updated', 0, { path: '/', domain: domain } );
				if(websiteId == 1){
					currentUrl = currentUrl.replace("/quotation/quote", "/us/quotation/quote");
				}else{
					currentUrl = currentUrl.replace("/us/quotation/quote", "/quotation/quote");
				}
				
				window.location.href = currentUrl;
            }
			
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
