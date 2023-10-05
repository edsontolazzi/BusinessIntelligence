require([
    'jquery',
    'Magento_Catalog/js/price-utils'
], function ($, priceUtils) {
    $(document).ready(function () {
        $('.currency-value').each(function () {
            let value = $(this).data('value');

            let formattedValue = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);

            $(this).text(formattedValue);
        });
    });
});
