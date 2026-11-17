<?php
/**
 * BFCM 2026 Banner & Admin Notice for Easy Twitter Feeds
 *
 * Handles the Black Friday / Cyber Monday 2026 promotion banner and notice.
 * Expiry: December 5, 2026.
 *
 * @package EasyTwitterFeeds
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ETF_BFCM_Notice' ) ) {
    class ETF_BFCM_Notice {

        private static $instance = null;

        /**
         * End date for BFCM 2026 promotion (December 5, 2026 23:59:59 UTC).
         */
        const EXPIRY_TIMESTAMP = 1796515199;

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            add_action( 'all_admin_notices', [ $this, 'render_all_twitter_feeds_banner' ] );
            add_action( 'wp_ajax_etf_dismiss_bfcm_notice', [ $this, 'ajax_dismiss' ] );
        }

        /**
         * Check if promotion is currently active and not dismissed.
         */
        public function is_active() {
            if ( time() > self::EXPIRY_TIMESTAMP ) {
                return false;
            }

            if ( get_user_meta( get_current_user_id(), 'etf_bfcm_2026_dismissed', true ) ) {
                return false;
            }

            if ( function_exists( 'easy_tf_fs' ) && easy_tf_fs()->can_use_premium_code() ) {
                return false;
            }

            return true;
        }

        /**
         * AJAX handler to persist dismissal for the user.
         */
        public function ajax_dismiss() {
            check_ajax_referer( 'etf_bfcm_dismiss_nonce', 'nonce' );

            if ( ! current_user_can( 'read' ) ) {
                wp_send_json_error( [ 'message' => 'Unauthorized' ] );
            }

            update_user_meta( get_current_user_id(), 'etf_bfcm_2026_dismissed', 1 );
            wp_send_json_success();
        }

        /**
         * Render the BFCM banner on the All Twitter Feeds list page (edit.php?post_type=easy-twitter-feeds).
         */
        public function render_all_twitter_feeds_banner() {
            global $pagenow;

            if ( 'edit.php' !== $pagenow || ! isset( $_GET['post_type'] ) || 'easy-twitter-feeds' !== $_GET['post_type'] || isset( $_GET['page'] ) ) {
                return;
            }

            $screen = get_current_screen();
            if ( $screen && 'edit-easy-twitter-feeds' !== $screen->id ) {
                return;
            }

            if ( ! $this->is_active() ) {
                return;
            }

            $nonce       = wp_create_nonce( 'etf_bfcm_dismiss_nonce' );
            $pricing_url = 'https://bplugins.com/products/easy-twitter-feeds/pricing/?utm_source=all_twitter_feeds&utm_medium=bfcm_banner&utm_campaign=bfcm2026';
            ?>
            <div id="etfBfcmBannerWrap" style="display:none;">
                <style>
                    /* ===== BFCM Banner: Full-width with 20px right gap ===== */
                    #etfBfcmBannerWrap {
                        display: none;
                        width: 100%;
                        box-sizing: border-box;
                        padding-right: 20px;
                        margin: 0;
                        clear: both;
                    }
                    #etfBfcmBannerWrap .etfBfcmBanner {
                        display: flex;
                        align-items: stretch;
                        background: #ffffff;
                        border: 1px solid #d1e1fb;
                        border-left: none;
                        border-radius: 0;
                        box-shadow: 0 2px 12px rgba(20, 110, 245, 0.08);
                        margin: 0;
                        width: 100%;
                        overflow: hidden;
                        position: relative;
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                        box-sizing: border-box;
                    }
                    #etfBfcmBannerWrap .etfBfcmBanner * {
                        box-sizing: border-box;
                    }
                    #etfBfcmBannerWrap .etfBfcmRibbon {
                        background: linear-gradient(135deg, #EEF4FF 0%, #E3EDFE 50%, #D8E6FD 100%);
                        border-right: 1px solid #d1e1fb;
                        padding: 12px 18px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-width: 140px;
                        flex-shrink: 0;
                    }
                    #etfBfcmBannerWrap .etfBfcmRibbonIconWrap {
                        width: 44px;
                        height: 44px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    #etfBfcmBannerWrap .etfBfcmRibbonIcon,
                    #etfBfcmBannerWrap .etfBfcmRibbonIconWrap svg {
                        width: 100%;
                        height: 100%;
                        display: block;
                        object-fit: contain;
                        filter: drop-shadow(0 4px 10px rgba(20, 110, 245, 0.25));
                    }
                    #etfBfcmBannerWrap .etfBfcmRibbonTitle {
                        color: #001737;
                        font-size: 10px;
                        font-weight: 800;
                        letter-spacing: 0.8px;
                        margin-top: 6px;
                        text-transform: uppercase;
                        text-align: center;
                        line-height: 1.2;
                    }
                    #etfBfcmBannerWrap .etfBfcmContent {
                        flex: 1;
                        padding: 12px 20px;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        gap: 6px;
                    }
                    #etfBfcmBannerWrap .etfBfcmHeaderRow {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        flex-wrap: wrap;
                    }
                    #etfBfcmBannerWrap .etfBfcmTitle {
                        margin: 0;
                        font-size: 15px;
                        font-weight: 700;
                        color: #070127;
                        line-height: 1.3;
                    }
                    #etfBfcmBannerWrap .etfBfcmDesc {
                        margin: 3px 0 8px 0;
                        font-size: 12px;
                        line-height: 1.5;
                        color: #4b5563;
                        max-width: 950px;
                    }
                    #etfBfcmBannerWrap .etfBfcmFeaturesRow {
                        display: flex;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 16px;
                    }
                    #etfBfcmBannerWrap .etfBfcmFeatureItem {
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        font-size: 12px;
                        color: #4b5563;
                        font-weight: 500;
                    }
                    #etfBfcmBannerWrap .etfBfcmCheckmark {
                        color: #070127;
                        font-weight: 700;
                        font-size: 12px;
                    }
                    #etfBfcmBannerWrap .etfBfcmAction {
                        display: flex;
                        align-items: center;
                        padding-right: 22px;
                        padding-left: 10px;
                        position: relative;
                        flex-shrink: 0;
                    }
                    #etfBfcmBannerWrap .etfBfcmCloseBtn {
                        position: absolute;
                        top: 8px;
                        right: 10px;
                        background: transparent;
                        border: none;
                        cursor: pointer;
                        color: #0b81ee;
                        font-size: 18px;
                        line-height: 1;
                        padding: 2px 6px;
                        border-radius: 4px;
                        transition: color 0.15s ease, background 0.15s ease;
                    }
                    #etfBfcmBannerWrap .etfBfcmCloseBtn:hover {
                        color: #0860b3;
                        background: rgba(11, 129, 238, 0.1);
                    }
                    #etfBfcmBannerWrap .etfBfcmCtaBtn {
                        margin-right: 20px;
                        background: #e3edfe;
                        color: #0b81ee !important;
                        text-decoration: none !important;
                        font-size: 13px;
                        font-weight: 600;
                        padding: 9px 18px;
                        border-radius: 6px;
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        transition: all 0.2s ease;
                        box-shadow: none !important;
                        border: 1px solid rgba(11, 129, 238, 0.25);
                        white-space: nowrap;
                    }
                    #etfBfcmBannerWrap .etfBfcmCtaBtn:hover {
                        background: #d2e3fc;
                        color: #0860b3 !important;
                        transform: translateY(-1px);
                    }
                    @media (max-width: 900px) {
                        #etfBfcmBannerWrap { padding-right: 12px; }
                        #etfBfcmBannerWrap .etfBfcmBanner { flex-direction: column; border-left: 1px solid #d1e1fb; }
                        #etfBfcmBannerWrap .etfBfcmRibbon { width: 100%; flex-direction: row; gap: 12px; padding: 8px 16px; border-right: none; border-bottom: 1px solid #d1e1fb; }
                        #etfBfcmBannerWrap .etfBfcmContent { padding: 12px 16px; }
                        #etfBfcmBannerWrap .etfBfcmAction { padding: 0 16px 12px; justify-content: flex-start; }
                        #etfBfcmBannerWrap .etfBfcmCloseBtn { top: 8px; right: 8px; }
                    }
                </style>
                <div class="etfBfcmBanner">
                    <div class="etfBfcmRibbon">
                        <div class="etfBfcmRibbonIconWrap">
                            <?php echo $this->get_logo_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                        <span class="etfBfcmRibbonTitle">TWITTER FEEDS</span>
                    </div>

                    <div class="etfBfcmContent">
                        <div class="etfBfcmHeaderRow">
                            <h3 class="etfBfcmTitle"><?php esc_html_e( "Easy Twitter Feeds's Biggest Black Friday Sale is Live – Up to 45% OFF!", 'easy-twitter-feeds' ); ?></h3>
                        </div>

                        <p class="etfBfcmDesc"><?php esc_html_e( 'The biggest deal of the year just arrived early! This is your once-a-year opportunity to upgrade to Easy Twitter Feeds Pro at an exclusive price. Unlock live auto-refresh, media gallery layouts, custom hashtag streams, and everything you need to showcase dynamic X feeds!', 'easy-twitter-feeds' ); ?></p>

                        <div class="etfBfcmFeaturesRow">
                            <span class="etfBfcmFeatureItem"><span class="etfBfcmCheckmark">✓</span> <?php esc_html_e( 'Live Auto-Refresh', 'easy-twitter-feeds' ); ?></span>
                            <span class="etfBfcmFeatureItem"><span class="etfBfcmCheckmark">✓</span> <?php esc_html_e( 'Media Gallery Layouts', 'easy-twitter-feeds' ); ?></span>
                            <span class="etfBfcmFeatureItem"><span class="etfBfcmCheckmark">✓</span> <?php esc_html_e( 'Custom Hashtag Feeds', 'easy-twitter-feeds' ); ?></span>
                            <span class="etfBfcmFeatureItem"><span class="etfBfcmCheckmark">✓</span> <?php esc_html_e( 'User Timeline & Likes', 'easy-twitter-feeds' ); ?></span>
                        </div>
                    </div>

                    <div class="etfBfcmAction">
                        <button type="button" class="etfBfcmCloseBtn" id="etfBfcmCloseBtn" aria-label="Dismiss banner" title="Dismiss">&times;</button>
                        <a href="<?php echo esc_url( $pricing_url ); ?>" target="_blank" rel="noopener noreferrer" class="etfBfcmCtaBtn">
                            <?php esc_html_e( 'Unlock PRO Features →', 'easy-twitter-feeds' ); ?>
                        </a>
                    </div>
                </div>
            </div>
            <script>
            (function($) {
                $(document).ready(function() {
                    if (localStorage.getItem('etf_bfcm_2026_dismissed')) {
                        return;
                    }

                    $('#etfBfcmBannerWrap').show();

                    $('#etfBfcmCloseBtn').on('click', function(e) {
                        e.preventDefault();
                        $('#etfBfcmBannerWrap').slideUp(200, function() {
                            $(this).remove();
                        });

                        localStorage.setItem('etf_bfcm_2026_dismissed', '1');

                        $.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                            action: 'etf_dismiss_bfcm_notice',
                            nonce: '<?php echo esc_js( $nonce ); ?>'
                        });
                    });
                });
            })(jQuery);
            </script>
            <?php
        }

        /**
         * Return inlined official SVG logo of Easy Twitter Feeds.
         */
        private function get_logo_svg() {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="44" height="44" role="img" aria-label="Easy Twitter Feeds">
  <defs>
    <linearGradient id="etf-p-bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3B868B"/><stop offset="1" stop-color="#275677"/></linearGradient>
    <linearGradient id="etf-p-av" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5cc0c4"/><stop offset="1" stop-color="#3B868B"/></linearGradient>
    <linearGradient id="etf-p-hg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff6f83"/><stop offset="1" stop-color="#ff3b57"/></linearGradient>
    <clipPath id="etf-p-clip"><rect width="256" height="256" rx="56"/></clipPath>
    <clipPath id="etf-p-fclip"><rect x="78" y="78" width="100" height="132" rx="10"/></clipPath>
    <path id="etf-p-hpath" d="M0,4.2 C-6,-1.2 -10.5,-5.4 -6.6,-9.6 C-4.2,-12 -0.6,-10.6 0,-6.6 C0.6,-10.6 4.2,-12 6.6,-9.6 C10.5,-5.4 6,-1.2 0,4.2 Z"/>
    <g id="etf-p-card">
      <rect x="84" y="0" width="88" height="42" rx="9" fill="#ffffff"/>
      <circle cx="100" cy="16" r="9" fill="url(#etf-p-av)"/>
      <circle cx="100" cy="13.4" r="3.1" fill="#ffffff"/>
      <path d="M94 20.5a6 6 0 0 1 12 0z" fill="#ffffff"/>
      <rect x="114" y="10" width="34" height="6" rx="3" fill="#b9d6d8"/>
      <rect x="114" y="20" width="22" height="5" rx="2.5" fill="#e2e6f5"/>
      <rect x="92" y="30" width="56" height="5" rx="2.5" fill="#e6eaf1"/>
      <g transform="translate(157,32) scale(.62)"><use href="#etf-p-hpath" fill="url(#etf-p-hg)"/></g>
    </g>
  </defs>
  <g clip-path="url(#etf-p-clip)">
    <rect width="256" height="256" fill="url(#etf-p-bg)"/>
    <rect x="66" y="32" width="124" height="192" rx="26" fill="#ffffff" opacity="0.14"/>
    <rect x="66" y="32" width="124" height="192" rx="26" fill="none" stroke="#ffffff" stroke-opacity="0.5" stroke-width="2"/>
    <rect x="86" y="48" width="46" height="9" rx="4.5" fill="#ffffff" opacity="0.9"/>
    <rect x="86" y="62" width="30" height="6" rx="3" fill="#ffffff" opacity="0.55"/>
    <circle cx="170" cy="53" r="5" fill="#ff3b57"/>
    <g clip-path="url(#etf-p-fclip)">
      <g transform="translate(0,80)"><use href="#etf-p-card"/></g>
      <g transform="translate(0,128)"><use href="#etf-p-card"/></g>
      <g transform="translate(0,176)"><use href="#etf-p-card"/></g>
    </g>
    <rect x="78" y="78" width="100" height="10" fill="#ffffff" opacity="0.14"/>
    <g transform="translate(46,188)">
      <circle cx="0" cy="0" r="18" fill="#ffffff"/>
      <g scale="1.25"><use href="#etf-p-hpath" fill="url(#etf-p-hg)"/></g>
    </g>
  </g>
</svg>';
        }
    }

    ETF_BFCM_Notice::get_instance();
}

