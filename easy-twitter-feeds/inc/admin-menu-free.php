<?php
if (!defined('ABSPATH')) {exit;}
if(!class_exists('etfAdminMenu')) {

    class etfAdminMenu
    {
        public function __construct()
        {
            add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
            add_action('admin_menu', [$this, 'adminMenu']);
        }

        public function adminEnqueueScripts($hook)
        {
            if( strpos( $hook, 'easy-twitter-feeds' ) ){
                wp_enqueue_style( 'etf-admin-dashboard', ETF_DIR_URL . 'build/admin-dashboard.css', [], ETF_VERSION );
                wp_enqueue_script( 'etf-admin-dashboard', ETF_DIR_URL . 'build/admin-dashboard.js', [ 'react', 'react-dom',], ETF_VERSION, true );
                wp_set_script_translations( 'etf-admin-dashboard', 'easy-twitter', ETF_DIR_PATH . 'languages' );   
            }
        }

        public function adminMenu(){

            $menuIcon = "<svg xmlns='http://www.w3.org/2000/svg' x='0px' y='0px' width='48' height='48' viewBox='0 0 48 48'>
        <path fill='#03A9F4' d='M42,12.429c-1.323,0.586-2.746,0.977-4.247,1.162c1.526-0.906,2.7-2.351,3.251-4.058c-1.428,0.837-3.01,1.452-4.693,1.776C34.967,9.884,33.05,9,30.926,9c-4.08,0-7.387,3.278-7.387,7.32c0,0.572,0.067,1.129,0.193,1.67c-6.138-0.308-11.582-3.226-15.224-7.654c-0.64,1.082-1,2.349-1,3.686c0,2.541,1.301,4.778,3.285,6.096c-1.211-0.037-2.351-0.374-3.349-0.914c0,0.022,0,0.055,0,0.086c0,3.551,2.547,6.508,5.923,7.181c-0.617,0.169-1.269,0.263-1.941,0.263c-0.477,0-0.942-0.054-1.392-0.135c0.94,2.902,3.667,5.023,6.898,5.086c-2.528,1.96-5.712,3.134-9.174,3.134c-0.598,0-1.183-0.034-1.761-0.104C9.268,36.786,13.152,38,17.321,38c13.585,0,21.017-11.156,21.017-20.834c0-0.317-0.01-0.633-0.025-0.945C39.763,15.197,41.013,13.905,42,12.429'></path></svg>";

            add_menu_page(
                __('Easy Twitter', 'easy-twitter'),
                __('Easy Twitter', 'easy-twitter'),
                'manage_options',
                'easy-twitter-feeds',
                [$this, 'helpPage'],
                'data:image/svg+xml;base64,' . base64_encode($menuIcon),
                6
            );

             add_submenu_page(
                'easy-twitter-feeds',
                __('Demo and Help', 'easy-twitter'),
                __('Demo and Help', 'easy-twitter'),
                'manage_options',
                'easy-twitter-feeds',
                [$this, 'helpPage'],
            ); 
        }

        public function helpPage()
        {?>
            <div id='etfDashboard'
            data-info='<?php echo esc_attr( wp_json_encode( [
                    'version' => ETF_VERSION,
                    'isPremium' => etfIsPremium(),
                    'hasPro' => ETF_IS_PRO
                ] ) ); ?>'
            ></div>
        <?php }
    }
    new etfAdminMenu();
}