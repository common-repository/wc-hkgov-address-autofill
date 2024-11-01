<?php
class HKAF_HKGovAddressAutofill
{

    protected $option_name = 'hkaf_options';

    public function __construct()
    {
        //adding filters
        add_filter("plugin_action_links_" . WCGAAW_BASE, array(
            $this,
            'hkaf_settings_link'
        ));
        //adding actions
        add_action('admin_menu', array(
            $this,
            'hkaf_admin_menu'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'hkaf_enqueue_script'
        ));
        add_action('woocommerce_before_checkout_billing_form', array(
            $this,
            'hkaf_billing_checkout_field'
        ));
        add_action('woocommerce_before_checkout_shipping_form', array(
            $this,
            'hkaf_shipping_checkout_field'
        ));
    }

    /**
     * Creating custom field and icon for autocomplete
     *
     * @param mixed
     * @return empty
     *
     */
    public function hkaf_billing_checkout_field($checkout)
    {
        if (get_option('hkaf_enable_for_billing', '1') == '1')
        {
            woocommerce_form_field('billing_search_address', array(
                'type' => 'text',
                'class' => array('billing-autofill-field form-row-wide') ,
                'label' => __('Search address', 'wc-hkgov-address-autofill'),
                'placeholder' => __('Search for your Hong Kong address', 'wc-hkgov-address-autofill'),
                'autocomplete' => 'off'
            ) , $checkout->get_value('billing_search_address'));
        }
    }

    /**
     * Creating custom field and icon for autocomplete
     *
     * @param mixed
     * @return empty
     *
     */
    public function hkaf_shipping_checkout_field($checkout)
    {
        if (get_option('hkaf_enable_for_shipping', '1') == '1')
        {
            woocommerce_form_field('shipping_search_address', array(
                'type' => 'text',
                'class' => array('shipping-autofill-field form-row-wide'),
                'label' => __('Search address', 'wc-hkgov-address-autofill'),
                'placeholder' => __('Search for your Hong Kong address', 'wc-hkgov-address-autofill'),
                'autocomplete' => 'off'
            ) , $checkout->get_value('shipping_search_address'));
        }
    }

    /**
     * Setting link on plugin page
     *
     * @param array
     * @return array
     *
     */
    public function hkaf_settings_link($links)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=hkaf-options') . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Function for including scripts and style
     *
     * @param empty
     * @return mixed
     *
     */
    public function hkaf_enqueue_script()
    {
        $google_api_key = get_option('hkaf_google_place_api_key', '');
        if (!empty($google_api_key)) {
            wp_enqueue_script(
                'google-maps',
                'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&libraries=places',
                array('jquery'), 
                WCGAAW_PLUGIN_VERSION,
                true
            );
        }

        wp_enqueue_style('select2-style', plugins_url('assets/css/select2.css', __FILE__));
        wp_enqueue_script('select2', plugins_url('assets/js/select2.min.js', __FILE__) , array(
            'jquery'
        ) , '1.0.0', true);

        $url = plugin_dir_url(__FILE__) . '/assets/js/autofill.js';

        // adding scripts
        wp_register_script('hkaf-main', $url, array(
            'jquery',
            'select2',
        ) , WCGAAW_PLUGIN_VERSION, true);

        wp_enqueue_script('hkaf-main');

        $autofill_type = '';
        if (get_option('hkaf_enable_for_hkgov', '1') === '1') {
            $autofill_type = 'hkgov';
        } else if (get_option('hkaf_enable_for_google', '0') === '1') {
            $autofill_type = 'google';
        }

        wp_localize_script('hkaf-main', 'hkaf', array(
            'autofill_type' => $autofill_type,
            'autofill_for_billing' => get_option('hkaf_enable_for_billing', '1') ,
            'autofill_for_shipping' => get_option('hkaf_enable_for_shipping', '1') ,
        ));
    }

    /**
     * Function  for creating setting page in admin
     *
     * @param empty
     * @return empty
     *
     */
    public function hkaf_admin_menu()
    {
        add_options_page(__('HKGov Address Autofill for Woocommerce', 'wc-hkgov-address-autofill') , __('HKGov Address Autofill', 'wc-hkgov-address-autofill') , 'manage_options', 'hkaf-options', array(
            $this,
            'hkaf_admin_options'
        ));
    }

    /**
     * Admin option page form
     *
     * @param empty
     * @return empty
     *
     */
    public function hkaf_admin_options()
    {
        if (!current_user_can('manage_options'))
        { // Checking user can manage or not
            wp_die(__('You do not have sufficient permissions to access this page.', 'gaafw'));
        }
?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h1><?php echo wp_kses_post(__('HKGov Address Autofill for Woocommerce', 'wc-hkgov-address-autofill')); ?></h1>

			<?php
        global $active_tab;
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'hkaf-address-field-setting';
?>

			<h2 class="nav-tab-wrapper">
				<?php do_action('hkaf_settings_tab_heading'); ?>
			</h2>

			<form method="post" action="options.php" id="checkout-address-autocomplete-form">

				<?php do_action('hkaf_settings_tab_content'); ?>

				<div class = "hkaf_settings_tab_content_save_button">
					<?php submit_button(); ?>

				</div>

			</form>

		</div><!-- /.wrap -->

		<?php
    }
}

