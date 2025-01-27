<?php
/*
 * Plugin Name: Feeds For Twitter
 * Description: You can Embed your Twitter timeline feed, Follow widget anywhere in WordPress using Shortcode.  
 * Version: 1.2.10
 * Author: bPlugins
 * Author URI: https://bplugins.com/
 * Domain Path:  /languages
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:  easy-twitter-feeds
 */


// ABS PATH
if (!defined('ABSPATH')) { exit; }

if (function_exists('etf_fs')) {

    register_activation_hook(__FILE__, function () {
        if (is_plugin_active('easy-twitter-feeds/easy-twitter-feeds.php')) {
            deactivate_plugins('easy-twitter-feeds/easy-twitter-feeds.php');
        }
        if (is_plugin_active('easy-twitter-feeds-pro/easy-twitter-feeds.php')) {
            deactivate_plugins('easy-twitter-feeds-pro/easy-twitter-feeds.php');
        }
    });

} else {
    define('ETF_VERSION', '1.2.10');
    define('ETF_DIR_URL', plugin_dir_url(__FILE__));
    define('ETF_DIR_PATH', plugin_dir_path(__FILE__));
    define('ETF_IS_PRO', 'easy-twitter-feeds-pro/easy-twitter-feeds.php' === plugin_basename(__FILE__)); 
    // Create a helper function for easy SDK access.
    function etf_fs()
    {
        global $etf_fs;

        if (!isset($etf_fs)) {
             // Include Freemius SDK.
            if (file_exists(dirname(__FILE__) . '/bplugins_sdk/init.php')) {
                require_once dirname(__FILE__) . '/bplugins_sdk/init.php';
            }
            if (file_exists(dirname(__FILE__) . '/freemius/start.php')) {
                require_once dirname(__FILE__) . '/freemius/start.php';
            }
            $etf_fs = fs_lite_dynamic_init(
                array(
                   'id'                  => '14839',
                'slug'                => 'easy-twitter-feeds',
                'premium_slug'        => 'easy-twitter-feeds-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_ba9a28a91e7b8f97d024123dad59c',
                'is_premium'          => true,
                'premium_suffix'      => 'Pro',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                    'menu' => array(
                        'slug'           => 'edit.php?post_type=easy-twitter-feeds',
                        'first-path' => 'edit.php?post_type=easy-twitter-feeds',
                        'contact' => false,
                        'support' => false,
                    )
                )
            );
        }

        return $etf_fs;
    }

    etf_fs();
    do_action('etf_fs_loaded');

    class ETFPlugin
    {
        public function __construct()
        {
            add_action('init', [$this, 'onInit']);
            add_action('wp_ajax_etfPipeChecker', [$this, 'etfPipeChecker']);
            add_action('wp_ajax_nopriv_etfPipeChecker', [$this, 'etfPipeChecker']);
            add_action('admin_init', [$this, 'registerSettings']);
            add_action('rest_api_init', [$this, 'registerSettings']);
        }

        function onInit()
        {
            load_plugin_textdomain('easy-twitter', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        // function etfPipeChecker()
        // {
        //     $nonce = $_POST['_wpnonce'] ?? null;
        //     if (!wp_verify_nonce($nonce, 'wp_ajax'))
        //     {
        //         wp_send_json_error('Invalid Request');
        //     }
        //     wp_send_json_success([
        //         'isPipe' => etfIsPremium(),
        //     ]);
        // }

        public function etfPipeChecker() {
            $nonce = $_POST['_wpnonce'];

            if (!wp_verify_nonce($nonce, 'wp_ajax')) {
                wp_send_json_error('Invalid Request');
            }

            wp_send_json_success([
                'isPipe' => ETF_IS_PRO?\etf_fs()->is__premium_only() && \etf_fs()->can_use_premium_code() : false,
            ]);
        }

        function registerSettings()
        {
            register_setting('etfUtils', 'etfUtils', [
                'show_in_rest' => [
                    'name' => 'etfUtils',
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
                'type' => 'string',
                'default' => wp_json_encode([
                    'nonce' => wp_create_nonce('wp_ajax'),
                ]),
                'sanitize_callback' => 'sanitize_text_field',
            ]);
        }

    }
    new ETFPlugin();
    require_once ETF_DIR_PATH . 'inc/block.php';
    require_once ETF_DIR_PATH . 'inc/CustomPost.php';
    require_once ETF_DIR_PATH . 'inc/ShortCode.php';
}