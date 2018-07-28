define(
    [
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer',
    ],
    function(
        Component,
        ko,
        customer,
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'PleaseWork_CheckoutStep/shiny'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            initialize: function () {
                this._super(); //you must call super on components or they will not render
            }
        });
    }
);
