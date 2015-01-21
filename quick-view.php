<?php
/*
  Plugin Name: Woocommerce Product Quick View
  Plugin URI: http://www.unicodesystems.in
  Description: This plugin is used for adding the quick view functionality to your woocommerce store. Woocommerce plugin is pre-requisite for this plugin to run.
  Version: 1.0
  Author: Harshita
  Author URI: http://www.unicodesystems.in

 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('QV_PLUGIN_PATH'))
    define('QV_PLUGIN_PATH', plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . '/quick-view';

if (!defined('QV_PLUGIN_URL'))
    define('QV_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('QV_PLUGIN_JS_URL'))
    define('QV_PLUGIN_JS_URL', QV_PLUGIN_URL . 'js');

if (!defined('QV_PLUGIN_CSS_URL'))
    define('QV_PLUGIN_CSS_URL', QV_PLUGIN_URL . 'css');

register_activation_hook(__FILE__, 'child_plugin_activate');

function child_plugin_activate() {

    if (!is_plugin_active('woocommerce/woocommerce.php') and current_user_can('activate_plugins')) {
        wp_die('Sorry, this plugin requires Woocommerce Plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
    }
}

add_action('init', 'wp_enqueue');

function wp_enqueue() {

    wp_enqueue_script('fancybox', QV_PLUGIN_JS_URL . '/jquery.fancybox.js', array('jquery'));
    wp_enqueue_script('defaultscript', QV_PLUGIN_JS_URL . '/script.js', array('jquery'));

    wp_enqueue_style('fancybox', QV_PLUGIN_CSS_URL . '/jquery.fancybox.css');
    wp_enqueue_style('stylesheet', QV_PLUGIN_CSS_URL . '/qv-style.css');
}

add_action('woocommerce_before_single_product_summary', 'addingGallery');

function addingGallery() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $tn = $('.thumbnails');
            var $ti = $('.images');
            var src;
            $tn.find('a:first').addClass('selected');
            $tn.find('a').click(function(e) {
                e.preventDefault();
                $tn.find('a').removeClass('selected');
                src = $(this).attr('href');
                $(this).addClass('selected');
                $ti.find('a.active').attr('href', src);
                $ti.find('a.active img').attr('src', src);
            });
        });
    </script>


    <?php
}

add_action('woocommerce_before_shop_loop', 'addingScript');

function addingScript() {

    wp_dequeue_script('woocommerce');

    $text = apply_filters('quick_view_text', 'Quick View');
    ?>



    <script type="text/javascript">

        jQuery(document).ready(function($) {
            $('#content').append('<div id="view-content" style="display:none;"><div class="page-content"></div></div>');
            var text = '<?php echo $text; ?>';
            $('.product').each(function() {
                var id = $(this).find('a.add_to_cart_button').attr('data-product_id');
                var $af = $(this).find('a:first');
                var href = $af.attr('href');
                $(this).prepend('<span class="overlay-view-more id-' + id + '" style="display:none;"><a href="' + href + '" class="view-more" data-link="' + href + '" data-id="' + id + '"><span class="view-icon">' + text + '</span></a></span>');
            });
            var $product = $('.product');
            $product.mouseenter(function() {
                $(this).find('.overlay-view-more').addClass('current').show();
            });
            $product.mouseleave(function() {
                $(this).find('.overlay-view-more').removeClass('current').hide();
            });
            var pid, nid, index, pi, ni;
            var lst = $('li.product:last').index();
            var frt = $('li.product:first').index();
            var $product = $('.product');
            function checkPi() {
                if (pi < frt) {
                    index = frt;
                    pi = lst;
                }
            }
            function checkNi() {
                if (ni > lst) {
                    index = lst;
                    ni = frt;
                }
            }
            $product.on('click', '.view-more', function(e) {
                e.preventDefault();
                $.fancybox.showLoading();
                $('body').prepend('<div class="overlay"><a href="" class="prev-prod">Prev</a><a href="" class="next-prod">Next</a></div>');
                index = $(this).parents('li').index();
                pi = index - 1;
                ni = index + 1;
                pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                var url = $(this).attr('data-link');
                checkPi();
                checkNi();
                callfancy(url);
                $('.input-text.qty').attr('size', 1);
            });


            $('.prev-prod').live('click', function(e) {
                e.preventDefault();
                if ((typeof pid !== 'undefined') || pid > 0) {
                    $(this).removeClass('disabled');
                    $.fancybox.close();
                    var url = $('li.post-' + pid).find('a.view-more').attr('data-link');
                    index = pi;
                    ni = index + 1;
                    pi--;
                    checkPi();
                    jQuery('.quantity .decrement').die('click');
                    jQuery('.quantity .increment').die('click');
                    pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                    nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                    callfancy(url);
                }
                return false;
            });
            $('.next-prod').live('click', function(e) {
                e.preventDefault();
                if (typeof nid !== 'undefined') {
                    $(this).removeClass('disabled');
                    $.fancybox.close();
                    var url = $('li.post-' + nid).find('a.view-more').attr('data-link');
                    index = ni;
                    pi = index - 1;
                    ni++;
                    checkNi();
                    jQuery('.quantity .decrement').die('click');
                    jQuery('.quantity .increment').die('click');
                    pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                    nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                    callfancy(url);
                }
                return false;
            });
        });
    </script>    

    <?php
}

