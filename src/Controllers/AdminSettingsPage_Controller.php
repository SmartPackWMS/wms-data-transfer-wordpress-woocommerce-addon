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
                'webhook_key',
                __('Access token', 'smartpack_wms'),
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

            add_settings_field(
                'nonce_key',
                __('Nonce Key', 'smartpack_wms'),
                function () use ($options) {
                    echo '<input 
                        type="text" 
                        name="' . $this->_prefix . 'settings[nonce_key]" 
                        value="' . ($options['nonce_key'] ??  '') . '">
                    ';
                },
                'pluginPage',
                $this->_prefix . 'pluginPage_section'
            );
        });
    }
}
