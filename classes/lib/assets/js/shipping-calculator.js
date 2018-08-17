(function ($) {

    $(document).ready(function () {
        
        $(".btn_shipping").click(function () {
            $(".wp_melhor_envio_shiiping_form").toggle("slow");
        });

        $('.wp_melhor_envio_calc_shipping').click(function () {
            $(".loaderimage").show();
            var datastring = $(this).closest(".woocommerce-shipping-calculator").serialize();
            if($("input.variation_id").length>0){
                datastring=datastring+"&variation_id="+$("input.variation_id").val();
            }
            if($("input[name=quantity]").length>0){
                datastring=datastring+"&current_qty="+$("input[name=quantity]").val();
            }

            $.ajax({
                type: "POST",
                url: wp_melhor_envio_ajax_url+"?action=ajax_calc_shipping",
                data: datastring,
                success: function (data) {  
                    $(".loaderimage").hide();
                    $(".wp_melhor_envio_message").removeClass("wp_melhor_envio_error").removeClass("wp_melhor_envio_success");
                    $("#responseMelhorEnvio").html(data);
                }
            });
            return false;
        });
    });
})(jQuery);