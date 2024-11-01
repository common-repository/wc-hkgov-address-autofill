<?php
/** 
 * Plugin Name: Autofill HKGov Address For WC
 * Plugin URI: https://www.github.com/kyktommy/autofill-hkgov-address-for-wc
 * Description: Search and autofill the checkout form automatically with hk gov address or google place api.
 * Version: 1.0.5
 * Author: kyktommy
 * Author URI: https://kyktommy.github.io
 * Text Domain: wc-hkgov-address-autofill
 * Tested up to: 5.8.3
 *  License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;
// defining basename
define('WCGAAW_BASE', plugin_basename(__FILE__));
define('WCGAAW_PLUGIN_PATH', dirname(__FILE__));
define('WCGAAW_PLUGIN_DIR', plugin_dir_url(__DIR__));
define('WCGAAW_PLUGIN_VERSION', '1.0.5');
define('WCGAAW_PLUGIN_URL', plugins_url('', __FILE__));

if (!class_exists('WC_GAAInstallCheck'))
{
    //Restrict installation without woocommerce
    class WC_GAAInstallCheck
    {
        static function install()
        {
            /**
             * Check if WooCommerce  are active
             *
             */
            if (!class_exists('WooCommerce'))
            {
                apply_filters('active_plugins', get_option('active_plugins'));
                // Deactivate the plugin
                deactivate_plugins(__FILE__);

                // Throw an error in the wordpress admin console
                $error_message = __('This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>  plugins to be active!', 'woocommerce');
                die($error_message);
            }
        }
    }
}

register_activation_hook(__FILE__, array(
    'WC_GAAInstallCheck',
    'install'
));

define('WCGAAW_PLUGIN_NAME', 'HKGov Address AutoFill For WooCommerce');

//load textdomain
// add_action('plugins_loaded', 'hkaf_load_textdomain');
// function hkaf_load_textdomain() {
// 	load_plugin_textdomain( 'wc-hkgov-address-autofill', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );
// }
//Get required files
require_once dirname(__FILE__) . '/hkgov-address-autofill.php';

new HKAF_HKGovAddressAutofill();

// Checkout field setting-------------------------
require_once dirname(__FILE__) . '/includes/class-address-field-setting.php';

new HKAF_CheckoutFieldSetting();
