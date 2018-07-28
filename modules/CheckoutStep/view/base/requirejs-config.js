var config = {
    'config': {
        'mixins': {
            'Magento_Checkout/js/view/shipping': {
                'PleaseWork_CheckoutStep/js/view/shipping-payment-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'PleaseWork_CheckoutStep/js/view/shipping-payment-mixin': true
            }
        }
    }
}