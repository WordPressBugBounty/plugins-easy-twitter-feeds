<?php

/*
 * Plugin Name: Easy Twitter Feeds
 * Plugin URI:  https://twitter-feed.bplugins.com/
 * Description: You can Embed your Twitter timeline feed, Follow widget anywhere in WordPress using Shortcode.  
 * Version: 1.2.12
 * Author: bPlugins LLC
 * Author URI: https://bplugins.com/
 * Text Domain: easy-twitter
 * Domain Path:  /languages
 * License: GPLv3
 * @fs_free_only, /freemius-lite, bsdk_config.json,
 */
// ABS PATH
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'etf_fs' ) ) {
    etf_fs()->set_basename( false, __FILE__ );
} else {
    // Constants
    define( 'ETF_ASSETS_DIR', plugin_dir_url( __FILE__ ) . 'assets/' );
    define( 'ETF_IS_PRO', file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) );
    define( 'ETF_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.2.12' ) );
    define( 'ETF_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'ETF_DIR_PATH', plugin_dir_path( __FILE__ ) );
    if ( !function_exists( 'etf_fs' ) ) {
        // Create a helper function for easy SDK access.
        function etf_fs() {
            global $etf_fs;
            if ( !isset( $etf_fs ) ) {
                // Include Freemius SDK.
                if ( ETF_IS_PRO ) {
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    require_once dirname( __FILE__ ) . '/inc/LicenseActivation.php';
                } else {
                    require_once dirname( __FILE__ ) . '/freemius-lite/start.php';
                }
                $etfConfig = array(
                    'id'                  => '14839',
                    'slug'                => 'easy-twitter-feeds',
                    'premium_slug'        => 'easy-twitter-feeds-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_ba9a28a91e7b8f97d024123dad59c',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'menu'                => array(
                        'slug'       => 'edit.php?post_type=easy-twitter-feeds',
                        'first-path' => 'edit.php?post_type=easy-twitter-feeds&page=easy-twitter-feeds#/pricing',
                        'support'    => false,
                    ),
                );
                $etf_fs = ( ETF_IS_PRO ? fs_dynamic_init( $etfConfig ) : fs_lite_dynamic_init( $etfConfig ) );
            }
            return $etf_fs;
        }

        // // Init Freemius.
        etf_fs();
        // Signal that SDK was initiated.
        do_action( 'etf_fs_loaded' );
    }
    require_once ETF_DIR_PATH . 'inc/CustomPost.php';
    require_once ETF_DIR_PATH . 'inc/ShortCode.php';
    function etfIsPremium() {
        return ( ETF_IS_PRO ? etf_fs()->can_use_premium_code() : false );
    }

    class BPETF_EASY_TWITTER_FEEDS {
        public function __construct() {
            $this->load_classes();
            add_action( 'init', [$this, 'onInit'] );
            add_action( 'enqueue_block_editor_assets', [$this, 'enqueueBlockEditorAssets'] );
            add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
        }

        function onInit() {
            register_block_type( __DIR__ . '/build' );
        }

        public function enqueueBlockEditorAssets() {
            wp_add_inline_script( 'etf-twitter-feed-editor-script', "const etfpipecheck=" . wp_json_encode( etfIsPremium() ) . ';', 'before' );
        }

        public function load_classes() {
            require_once plugin_dir_path( __FILE__ ) . '/inc/admin-menu.php';
        }

        public function adminEnqueueScripts( $hook ) {
            global $post_type;
            if ( $post_type == "easy-twitter-feeds" ) {
                wp_enqueue_style(
                    'mcbAdmin',
                    ETF_DIR_URL . 'assets/css/admin.css',
                    [],
                    ETF_VERSION
                );
                wp_enqueue_script(
                    'mcbAdmin',
                    ETF_DIR_URL . 'assets/js/admin.js',
                    ['wp-i18n'],
                    ETF_VERSION,
                    true
                );
            }
        }

    }

    new BPETF_EASY_TWITTER_FEEDS();
}