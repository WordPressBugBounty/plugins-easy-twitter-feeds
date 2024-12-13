<?php 
/*
 * Plugin Name: Feeds For Twitter
 * Description: You can Embed your Twitter timeline feed, Follow widget anywhere in WordPress using Shortcode.  
 * Version: 1.2.8
 * Author: bPlugins
 * Author URI: https://bplugins.com/
 * Text Domain:  easy-twitter-feeds
 * Domain Path:  /languages
 * License: GPLv3
 */


/*Some Set-up*/
define('ETF_PLUGIN_DIR', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' ); 
define('ETF_PLUGIN_VERSION', '1.2.8' ); 

add_action('plugin_loaded','etf_load_textdomain');
function etf_load_textdomain(){
    load_textdomain('easy-twitter-feeds', ETF_PLUGIN_DIR.'languages');

}

//Script and style
function etf_style_and_scripts() {
   // wp_enqueue_style( 'h5ap-style', plugin_dir_url( __FILE__ ) . 'public/style/plyr.css', array(), ETF_PLUGIN_VERSION , 'all' );
    wp_enqueue_script( 'widget-js', plugin_dir_url( __FILE__ ). 'public/js/widget.js' , array(), ETF_PLUGIN_VERSION , false );
}
add_action( 'wp_enqueue_scripts', 'etf_style_and_scripts' );


// Shortcode for Timeline
function etf_shortcode_func($atts){
	extract( shortcode_atts( array(

		'username' => null,
		'width' => null,
		'height' => null,
		'theme' => 'dark',
		'title' => 'Tweets by',

	), $atts ) );
?>	
<?php if (!empty($username)){ ?>
<a class="twitter-timeline" 
   data-width="<?php echo esc_attr($width); ?>" 
   data-height="<?php echo esc_attr($height); ?>" 
   data-theme="<?php echo esc_attr($theme); ?>" 
   href="https://twitter.com/<?php echo esc_attr($username); ?>">
   <?php echo esc_html($title); ?> <?php echo esc_html($username); ?>
</a>

<?php }else{ echo '<h2>You must enter your Twitter handle in the username attribute of the shortcode.  </h2>';}

}
add_shortcode('timeline','etf_shortcode_func');


// Shortcode for Follow button
function etf_shortcode_follow_func($atts) {
    $atts = shortcode_atts(
        array(
            'username' => null,
            'size'     => null,
            'count'    => null,
        ), 
        $atts
    );

    if (!empty($atts['username'])) {
        $username = sanitize_text_field($atts['username']);
        $size = esc_attr($atts['size']);
        $count = esc_attr($atts['count']);

        return sprintf(
            '<a href="https://twitter.com/%s" class="twitter-follow-button" data-size="%s" data-show-count="%s">Follow @%s</a>',
            $username, $size, $count, esc_html($username)
        );
    } else {
        return '<h2>' . esc_html__('You must enter your Twitter handle in the username attribute of the shortcode.', 'easy-twitter-feeds') . '</h2>';
    }
}
add_shortcode('follow_button', 'etf_shortcode_follow_func');