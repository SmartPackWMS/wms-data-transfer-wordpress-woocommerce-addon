<?php

namespace SmartPack\WMS\Controllers;

class AdminSettingsPage_Controller
{
    private $_prefix = 'smartpack_wms_plugin_';

    function init()
    {
        add_action('admin_menu', function () {
            add_options_page('SmartPack WMS', 'SmartPack WMS', 'manage_options', 'smartpack_wms', function () {

                echo '
                <form action="options.php" method="post">
                <h2>' . __('SmartPack WMS', 'smartpack_wms') . '</h2>
                ';

                settings_fields('pluginPage');
                do_settings_sections('pluginPage');
                submit_button();

                echo '
                </form>
                ';
            });
        });

        add_action('admin_init', function () {
            register_setting('pluginPage', $this->_prefix . 'settings');
            $options = get_option($this->_prefix . 'settings');

            add_settings_section(
                $this->_prefix . 'pluginPage_section',
                __('API Endpoint', 'smartpack_wms'),
                function () {
                    echo __('Settings for SmartPack WMS API integration', 'smartpack_wms');
                },
                'pluginPage'
            );

            add_settings_field(
                'endpoint',
                __('API Endpoint', 'smartpack_wms'),
                function () use ($options) {
                    echo '<input 
                        type="text" 
                        name="' . $this->_prefix . 'settings[endpoint]" 
                        value="' . ($options['endpoint'] ?? '') . '">
                    ';
                },
                'pluginPage',
                $this->_prefix . 'pluginPage_section'
            );

            add_settings_field(
                'username',
                __('Username', 'smartpack_wms'),
                function () use ($options) {
                    echo '<input 
                        type="text" 
                        name="' . $this->_prefix . 'settings[username]" 
                        value="' . ($options['username'] ?? '') . '">
                    ';
                },
                'pluginPage',
                $this->_prefix . 'pluginPage_section'
            );

            add_settings_field(
                'password',
                __('Password', 'smartpack_wms'),
                function () use ($options) {
                    echo '<input 
                        type="text" 
                        name="' . $this->_prefix . 'settings[password]" 
                        value="' . ($options['password'] ??  '') . '">
                    ';
                },
                'pluginPage',
                $this->_prefix . 'pluginPage_section'
            );

            add_settings_field(
                'webhook_key',
                __('Webhook Beartoken Access Key', 'smartpack_wms'),
                function () use ($options) {
                    echo '<input 
                        type="text" 
                        name="' . $this->_prefix . 'settings[webhook_key]" 
                        value="' . ($options['webhook_key'] ??  '') . '">
                    ';
                },
                'pluginPage',
                $this->_prefix . 'pluginPage_section'
            );
        });
    }
}
