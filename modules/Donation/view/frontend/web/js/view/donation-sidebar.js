define(
    [
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function (ko, Component, quote, totals, priceUtils) {
        "use strict";
        var checkoutConfig = window.checkoutConfig,
            donationConfig = checkoutConfig ? checkoutConfig.checkoutDonation : {};

        return Component.extend({
            defaults: {
                template: 'Sandftae_Donation/donation-sidebar'
            },

            isEnabled: donationConfig.donationEnable,

            getValue: function () {
                var price = totals.getSegment('donation').value;
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }
        });
    }
);