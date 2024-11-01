<?php
class HKAF_CheckoutFieldSetting
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // add plugin setting
        add_action('admin_init', array(
            $this,
            'hkaf_address_field_register_settings'
        ));

        add_action('hkaf_settings_tab_heading', array(
            $this,
            'hkaf_setting_address_field_heading'
        ));

        add_action('hkaf_settings_tab_content', array(
            $this,
            'hkaf_setting_address_field_content'
        ) , 1);

    }

    public function hkaf_address_field_register_settings()
    {
        register_setting('hkaf-field-settings-group', 'hkaf_enable_for_hkgov');
        register_setting('hkaf-field-settings-group', 'hkaf_enable_for_google');
        register_setting('hkaf-field-settings-group', 'hkaf_google_place_api_key');
        register_setting('hkaf-field-settings-group', 'hkaf_enable_for_billing');
        register_setting('hkaf-field-settings-group', 'hkaf_enable_for_shipping');
    }

    public function hkaf_setting_address_field_heading()
    {
        global $active_tab;
?>
        	<a href="?page=hkaf-options&tab=hkaf-address-field-setting" class="nav-tab <?php echo $active_tab == 'hkaf-address-field-setting' ? 'nav-tab-active' : ''; ?>">
				<?php echo __('Settings', 'wc-hkgov-address-autofill'); ?>
			</a>

        <?php
    }

    public function hkaf_setting_address_field_content()
    {
        global $active_tab;
?>
		<?php if ($active_tab == 'hkaf-address-field-setting'): ?>
            <?php settings_fields('hkaf-field-settings-group'); ?>
		    <?php do_settings_sections('hkaf-field-settings-group'); ?>
			<table class="form-table">

                <tr valign="top">

                    <th scope="row">
                        <?php echo __('Enable for hk gov address search', 'wc-hkgov-address-autofill'); ?>
                    </th>

                    <td>
                        <input type="checkbox" name="hkaf_enable_for_hkgov" value="1" <?php checked(1, get_option('hkaf_enable_for_hkgov', '1') , true); ?>>
                    </td>

                </tr>

                <tr valign="top">

                    <th scope="row">
                        <?php echo __('Enable for google address search', 'wc-hkgov-address-autofill'); ?>
                    </th>

                    <td>
                        <input type="checkbox" name="hkaf_enable_for_google" value="1" <?php checked(1, get_option('hkaf_enable_for_google', '0') , true); ?>>
                    </td>

                </tr>

                <tr valign="top">

                    <th scope="row">
                        <?php echo __('google place api key', 'wc-hkgov-address-autofill'); ?>
                    </th>

                    <td>
                        <input type="text" name="hkaf_google_place_api_key" value="<?php echo esc_attr(get_option('hkaf_google_place_api_key', '')); ?>">
                    </td>

                </tr>

                <tr valign="top">

                    <th scope="row">
                        <?php echo __('Enable for Billing address', 'wc-hkgov-address-autofill'); ?>
                    </th>

                    <td>
                        <input type="checkbox" name="hkaf_enable_for_billing" value="1" <?php checked(1, get_option('hkaf_enable_for_billing', '1') , true); ?>>
                    </td>

                </tr>

				<tr valign="top">

					<th scope="row">
						<?php echo __('Enable for Shipping address', 'wc-hkgov-address-autofill'); ?>
					</th>

					<td>
						<input type="checkbox" name="hkaf_enable_for_shipping" value="1" <?php checked(1, get_option('hkaf_enable_for_shipping', '1') , true); ?>>
					</td>

				</tr>

			</table>

	
		<?php
        endif; ?>

		<?php
    }

}

