function callfancy(url) {
    var content;
    jQuery('#view-content').html('');
    jQuery.fancybox.showLoading();
    jQuery('#view-content').load(url, function(response, status, xhr) {
        if (status == "error") {
            content = "Sorry but there was an error: ";
        } else {
            jQuery(this).find('.products,#secondary,.woocommerce-tabs,header,footer,.woocommerce-breadcrumb').remove();
            var product=jQuery(this).find('.product').clone();
            jQuery(this).find('#page').html(product);
            jQuery(this).find('.product').unwrap();
            var $tn = jQuery(this).find('.thumbnails');
            var $ti = jQuery(this).find('.images');
            var rlink = jQuery(this).find('.woocommerce-review-link').attr('href');
            jQuery(this).find('.woocommerce-review-link').attr('href', url + rlink);
            var src;
            $ti.find('a').each(function() {
                src = jQuery(this).attr('href');
                jQuery(this).find('img').attr('src', src);
            });
            var tia = jQuery(this).find('.images a:first').clone();
            $tn.prepend(tia);
            $ti.find('a:first').addClass('active');
            $tn.find('a').show().removeClass('first').removeClass('last');
            content = jQuery('#view-content').html();
            jQuery.fancybox.hideLoading();
            jQuery.fancybox({
                width: '80%',
                height: '80%',
                autoSize: false,
                closeClick: false,
                fitToView: false,
                openEffect: 'none',
                closeEffect: 'none',
                type: 'iframe',
                content: content,
                beforeShow: function() {
                    jQuery('.fancybox-wrap').addClass('quickview-product-box');
                },
                afterClose: function() {
                    jQuery('.overlay').remove();
                    jQuery('.quantity .increment').die('click');
                    jQuery('.quantity .decrement').die('click');
                },
                afterShow: function() {
                    setTimeout(function() {
                        jQuery('.quantity .minus').remove();
                        jQuery('.quantity .plus').remove();
                    }, 500);
                    jQuery( 'div.quantity' ).append( '<input type="button" value="+" class="increment" />' ).prepend( '<input type="button" value="-" class="decrement" />' );
                    jQuery('.quantity .qty').attr('value', 1);
                    jQuery('.quantity .decrement').live('click', function(e) {
                        e.preventDefault();
                        var value = jQuery('.quantity .qty').attr('value');
                        if (value > 1) {
                            value--;
                            jQuery('.quantity .qty').attr('value', value);
                            jQuery('.quantity .qty').val(value);
                            jQuery('.quantity .qty').trigger('change');
                        }
                        return false;
                    });
                    jQuery('.quantity .increment').live('click', function(e) {
                        e.preventDefault();
                        var value = jQuery('.quantity .qty').attr('value');
                        value++;
                        jQuery('.quantity .qty').attr('value', value);
                        jQuery('.quantity .qty').val(value);
                        jQuery('.quantity .qty').trigger('change');
                        return false;
                    });
                }
            });
        }
    });
}

