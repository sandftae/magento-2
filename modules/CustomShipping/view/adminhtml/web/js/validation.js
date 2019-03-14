require(
    [
        'jquery',
        'Sandftae_CustomShipping/js/api/data-sender',
        'mage/translate',
        'jquery/validate'
    ],
    function ($, sender) {
        $.validator.addMethod(
            'custom-validate-csv-file',
            function (v) {
                return sender();
            },
            $.mage.__(
                `Invalid CSV file. Check the file type and the equality of
                the structure of the loaded file and the table in the database`
            )
        )
    }
);