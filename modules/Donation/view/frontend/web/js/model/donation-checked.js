define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/totals',
    ],
    function ($, ko, Component, quote, urlBuilder, storage, getTotals, totals) {
        "use strict";
        var checkoutConfig = window.checkoutConfig,
            donationConfig = checkoutConfig ? checkoutConfig.checkoutDonation : {};

        return Component.extend({
            defaults: {
                template: 'Sandftae_Donation/donation-checked'
            },

            initObservable: function () {
                this._super()
                    .observe({
                        isDonate: false,
                    });
                return this;
            },

            getDonationOption: donationConfig.donationRates,

            isEnabled: donationConfig.donationEnable,

            getShortDescription: donationConfig.donationShortDescription,

            checkIsDonate: function () {
                if (!this.isDonate()) {
                    let donation = 0;
                    let quoteId = quote.getQuoteId();
                    this.setDonation(donation, quoteId);
                }
            },

            changeDonationAmount: function (element, event) {
                let quoteId = quote.getQuoteId();
                if ($(event.target).length && quoteId) {
                    let donation = $(event.target).prop('selected', true).val();
                    this.setDonation(donation, quoteId);
                }
            },

            setDonation: function (donation, quoteId) {
                let deferred = $.Deferred();
                let serviceUrl = urlBuilder.createUrl('/set-donation/:donationCost/:cartId', {
                    donationCost: donation,
                    cartId: quoteId
                });
                return storage.put(serviceUrl, false).done(
                    function (response) {
                        if (response) {
                            totals.isLoading(true);
                            getTotals(deferred);
                            $.when(deferred).done(function () {
                                totals.isLoading(false);
                            });
                        }
                    }
                ).fail(
                    function (response) {
                    }
                );
            }
        });
    }
);