define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function (
        ko,
        Component,
        _,
        stepNavigator
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PleaseWork_CheckoutStep/pleasework_checkout_step'
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    //step code will be used as step content id in the component template
                    'about-information',
                    //step alias
                    null,
                    //step title value
                    null,
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),
                    15
                );

                return this;
            },

            navigate: function () {

            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                stepNavigator.next();
            }
        });
    }
);