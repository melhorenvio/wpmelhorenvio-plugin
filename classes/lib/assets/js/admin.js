(function ($) {
    $(document).ready(function () {
        $('.color_picker').wpColorPicker();
    });
    $('#the-list').on('click', '.editinline', function () {
        var $post_id = $(this).closest('tr').attr('id');
        $post_id = $post_id.replace('post-', '');
        var $shipping_inline_data = $('#wp_melhor_enviowoo_shipping_inline_' + $post_id);
        if ($shipping_inline_data.find('._shipping_enable').html() == 'yes') {
            $('input[name="__calculator_hide"]', '.inline-quick-edit').attr("checked", true);
        }else{
            $('input[name="__calculator_hide"]', '.inline-quick-edit').attr("checked", false);
        }
    });
})(jQuery);