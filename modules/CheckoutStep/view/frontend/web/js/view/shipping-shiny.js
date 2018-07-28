define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/shipping'
    ],
    function(
        $,
        ko,
        Component
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'PleaseWork_CheckoutStep/shiny'
            },
            initialize: function () {
                var self = this;
                this._super();
            }
        });
    }
);