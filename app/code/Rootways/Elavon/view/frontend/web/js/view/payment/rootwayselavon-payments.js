define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
            
        rendererList.push(
            {
                type: 'rootways_elavon_option',
                component: 'Rootways_Elavon/js/view/payment/method-renderer/elavon-method'
            }
        );
            
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
