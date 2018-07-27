jQuery(document).ready(function(){

    var cep = jQuery('#billing_postcode').val();
    autocompleteAddressMelhorEnvio(cep);

    jQuery( document.body ).on( 'blur', '#billing_postcode', function() {
       var cep = jQuery('#billing_postcode').val();
       autocompleteAddressMelhorEnvio(cep);
    });
});

function autocompleteAddressMelhorEnvio(cep) {
    var url = 'https://viacep.com.br/ws/' + cep + '/json/';
    jQuery.post(url, function(data){ 
         jQuery('#billing_address_1').val(data.logradouro);
         jQuery('#billing_neighborhood').val(data.bairro);
         jQuery('#billing_city').val(data.localidade);
         jQuery('#billing_state').val(data.uf).trigger('change');

    });
}
