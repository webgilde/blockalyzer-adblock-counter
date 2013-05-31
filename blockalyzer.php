<?php
/*
  Plugin Name: BlockAlyzer - Adblock counter
  Version: 1.2.4
  Plugin URI: http://webgilde.com/en/blockalyzer/
  Description: Count how many of your visitors are using an adblock plugin.
  Author: Thomas Maier
  Author URI: http://www.webgilde.com/
  License: GPL v3

  adblock-counter Plugin for WordPress
  Copyright (C) 2013, webgilde GmbH, Thomas Maier (thomas.maier@webgilde.com)

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

//avoid direct calls to this file
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('BAVERSION', '1.2.4');
define('BANAME', 'blockalyzer-adblock-counter');
define('BATD', 'blockalyzer');
define('BADIR', basename(dirname(__FILE__)));
define('BAPATH', plugin_dir_path(__FILE__));

if (!class_exists('BA_CLASS')) {

    class BA_CLASS {

        /**
         * user id
         */
        public $_user_id = 0;

        /**
         * new user flag
         */
        public $_is_new_user = false;

        /**
         * methods for statistics
         */
        public $_stat_methods = array();
        
        /**
         * active stats methods
         */
        public $_active_stat_methods = array();
        
        /**
         * if any stats method is enabled, this is true
         */
        public $_is_measuring = false;
        
        /**
         * contains compare data
         * @since 1.2
         */
        public $_compare_data = array();
        
        /**
         * flag if compare is allowed
         * @since 1.2
         */
        public $_compare_allowed = false;
        
        /**
         * page hooks
         * @since 1.2
         */
        public $_hooks = array();
        
        /**
         * site categories
         */
        public $_site_categories = array();
        
        /**
         * plugin options
         */
        public $_options = array();

        /**
         * initialize the plugin
         * @updated 1.2.3
         */
        public function __construct() {

            // perform on plugin activation
            register_activation_hook(__FILE__, array($this, '_activation'));

            // load constant with adblock value
            add_action('init', array($this, 'load_adblock_constant'));
            // load statistic methods
            add_action('init', array($this, 'load_stat_methods'), 1);

            if ( is_admin() ) {
                // run if this was an upgrade
                $this->upgrade();
                
                add_action('admin_menu', array($this, 'add_stats_page'));
                add_action('admin_menu', array($this, 'add_settings_page'));
                add_action('admin_init', array($this, 'add_settings_options'));
                add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            }
            
            // everything connected with the measuing in the frontend
            if ( !is_admin() ) {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));    
                add_action('init', array($this, 'create_user_id'), 10);
                add_action('wp_head', array($this, 'head_js'), 1);
                add_action('wp_footer', array($this, 'include_bannergif'));
                add_action('wp_footer', array($this, 'display_footer'));
                
                // hooks for the standard stat method
                add_action('ba_js_footer', array($this, 'stat_method_standard_js'));                
            }
            
            // ajax call for logged in and not logged in users
            add_action('wp_ajax_standard_count', array($this, 'stat_method_standard_count'));
            add_action('wp_ajax_nopriv_standard_count', array($this, 'stat_method_standard_count'));
            add_action('wp_ajax_get_user_id', array($this, 'get_user_id'));
            add_action('wp_ajax_nopriv_get_user_id', array($this, 'get_user_id'));
            
            $this->_options = get_option('ba_settings', array() );
            
        }

        /**
         * load a constant with the information if adblock is enabled
         * false === adblock disabled
         * true === adblock enabled
         * 0 === not sure
         * usage: check with '== false' if you need to check if adblock is disabled or not sure (as default)
         * check if '=== false' or any of the other values to be sure about the status
         * @since 1.1.1
         */
        public function load_adblock_constant() {

            if (!defined('BA_ADBLOCK_ENABLED')) {

                if (isset($_COOKIE['BaAdBlock'])) {
                    if ($_COOKIE['BaAdBlock'] === 'disabled')
                        define('BA_ADBLOCK_ENABLED', false);
                    elseif ($_COOKIE['BaAdBlock'] === 'enabled')
                        define('BA_ADBLOCK_ENABLED', true);
                    else
                        define('BA_ADBLOCK_ENABLED', 0);
                } else {
                    define('BA_ADBLOCK_ENABLED', 0);
                }
            }
        }
        
        /**
         * load statistic methods
         */
        public function load_stat_methods() {
            // initialize basic method
            $stat_methods = array(
                'basic' => array(
                    'name' => __('Basic method', BATD),
                    'active' => 0,
                    'description' => sprintf(__('You can see your statistics in <em><a href="%s" title="Go to statistics page">Tools > AdBlock Stats</a></em>', BATD), admin_url('tools.php?page=adblock-counter')),
                )
            );
            // hook to register new stat methods
            $this->_stat_methods = apply_filters('ba_stat_methods', $stat_methods);
            
            // load information about active stat methods
            $this->get_active_stat_methods();
            
        }
        
        /**
         * get the information which stats method is enabled
         * loads the 'active' flag into $this->_stat_methods for each method
         * @todo what, if the number and kind of methods don't match?
         */
        public function get_active_stat_methods() {
            
            $active_stat_methods = array();
            
            if ( !empty( $this->_stat_methods ) && is_array( $this->_stat_methods )) {
                // get stat methods status
                $active_methods = $this->_options['methods'];
                if ( !empty( $active_methods ) && is_array( $active_methods ) ) {
                    foreach( $active_methods as $_method_key => $_active ) {
                        $this->_stat_methods[$_method_key]['active'] = $_active;
                        if ( $_active ) $active_stat_methods[] = $_method_key;
                    }
                }
            }
            
            $this->_active_stat_methods = $active_stat_methods;
            $this->_is_measuring = ( count( $this->_active_stat_methods ) > 0 ) ? 1 : 0;
        }

        /**
         * add statistics page for the default adblock counter
         */
        public function add_stats_page() {
            $this->_hooks['stats'] = add_management_page(__('BlockAlyzer Statistics', BATD), __('AdBlock Stats', BATD), 'manage_options', 'adblock-counter', array($this, 'render_stats_page'));
            add_action('load-'. $this->_hooks['stats'], array( $this, 'contextual_help'));
        }

        /**
         * add options page in tools section
         * @since 1.1.2
         */
        public function add_settings_page() {
            add_options_page(__('BlockAlyzer Settings', BATD), __('BlockAlyzer', BATD), 'manage_options', 'ba-settings-page', array($this, 'render_settings_page'));
        }

        /**
         * render the setting options
         * @since 1.1.2
         * @update 1.2.3
         */
        public function add_settings_options() {

            register_setting('ba_settings_group', 'ba_settings', array($this, 'sanitize_settings'));
            
            add_settings_section('ba_settings_section', __('Stats Method', BATD), array($this, 'render_settings_section'), 'ba-settings-page');

            // choose stats method
            if ( !empty( $this->_stat_methods ) && is_array( $this->_stat_methods )) { 
                $count = count( $this->_stat_methods );
                $i = 1;
                foreach( $this->_stat_methods as $_method_key => $_method ) {
                    // check if this is the last field
                    $last =  ( $count === $i++ ) ? 'last' : '';
                    add_settings_field('ba_methods_' . $_method_key, $_method['name'], array($this, 'render_settings_method'), 'ba-settings-page', 'ba_settings_section', array( $_method_key, $_method, $last ) );
                }       
            }     
            // options for benchmark page
            add_settings_field('ba_benchmark_category', __('Site Topic'), array($this, 'render_settings_benchmark_category'), 'ba-settings-page', 'ba_settings_section' );
            
        }
        
        /**
         * add contextual help
         * @since 1.2
         */
        public function contextual_help() {
            
            $screen = get_current_screen();
            if ($screen->id != $this->_hooks['stats']) return;       
            
            // conditions to send data
            $conditions = array(
                __('You can send a request every 3 hours', BATD),
                __('Your last reset was more than 24 hours ago', BATD),
                __('You have at least 20 visits and page views', BATD),
                __('You have at least 1 visit and page view with AdBlock', BATD),
            );
            
            $screen->add_help_tab( array(
                'id'	=> 'ba_conditions',
                'title'	=> __('Conditions', BATD),
                'content'	=> '<h3>' . __( 'Conditions under which your data will be accepted and compared with others.', BATD ) . '</h3><ul><li>' .
                    implode('</li><li>', $conditions ) . '</li></ul>',
            ) );
            
            // array with data we are sending to server; for localization
            $data_send = array(
                __('Hash - to check source', BATD),
                __('Domain - to prevent duplicate data', BATD),
                __('Language', BATD),
                __('Last reset - when have your data been reset (to prevent duplicate content', BATD),
                __('Number of Views', BATD),
                __('Number of View with AdBlock', BATD),
                __('Number of Unique Visitors', BATD),
                __('Number of Unique Visitors with AdBlock', BATD),
                __('Site topic (if specified)', BATD),
            );
            
            $screen->add_help_tab( array(
                'id'	=> 'ba_data',
                'title'	=> __('Data you send', BATD),
                'content'	=> '<h3>' . __( 'List of the data you send to our server.', BATD ) . '</h3><ul><li>' .
                    implode('</li><li>', $data_send ) . '</li></ul>',
            ) );
            
            // content for help tab with data we return
            $data_return = array(
                __('general Benchmark with page views and unique users for your localization', BATD),                
                __('if site topic provided: benchmark for your category and localization', BATD),                
            );
            
            $screen->add_help_tab( array(
                'id'	=> 'ba_return',
                'title'	=> __('Data you get', BATD),
                'content'	=> '<h3>' . __( 'List of the data you get from our server.', BATD ) . '</h3><ul><li>' .
                    implode('</li><li>', $data_return ) . '</li></ul>',
            ) );
            
        }
        
        /**
         * callback for option to choose the method of measurement
         * @param array $method with 1. value as index, second array with method information
         * @since 1.1.2
         */
        public function render_settings_method( $method ) {

            ?><input name="ba_settings[methods][<?php echo $method[0]; ?>]" id="ba_methods_<?php echo $method[0]; ?>" type="checkbox" value="1" <?php 
            checked(1, $method[1]['active'] ) ?>/><span class="description"><?php echo $method[1]['description']; ?></span><?php
            
            // if this is the last field, start another block of options
            if ( !empty($method[2]) && $method[2] == 'last' ) {
                ?></td></tr></tbody></table>
                </div><!-- .postbox -->
                <div class="postbox isc-setting-group">
                <h3 class="setting-group-head"><?php _e('Benchmark', BATD) ?></h3>
                <table class="form-table"><tbody><tr><td>
                <?php
            }
            
        }
        
        /**
         * render select field for benchmark category
         * @since 1.2.2
         * @updated 1.2.3
         */
        public function render_settings_benchmark_category( ) {

            $categories = $this->get_site_categories();
            if ( empty( $categories ) ) return __('Couldn\'t find any value to choose from', BATD );
            ?><select id="benchmark_category" name="ba_settings[benchmark_category]"><?php
                foreach ( $categories as $_key => $_element ) :
                    ?><option value="<?php echo $_key; ?>" <?php selected( $this->_options['benchmark_category'], $_key ); ?>><?php echo $_element; ?></option><?php
                endforeach;
            ?></select>
            <p class="description"><?php _e('If you enter your sites topic, you will receive additional benchmark data.', BATD ); ?></p>
            <?php
        }
        
        /**
         * sanitize the option values
         * especially include current values if not send via checkbox
         * @since 1.1.2
         * @updated 1.2.3
         */
        public function sanitize_settings ( $options ) {
            
            if ( isset( $options['methods'] ) && !empty( $this->_stat_methods ) && is_array( $this->_stat_methods )) {
                
                foreach ( $this->_stat_methods as $_key => $_method ) {
                    if ( !isset( $options['methods'][ $_key ] ) ) {
                        $options['methods'][ $_key ] = 0;
                    }
                }
                
            }
            
            return $options;   
        }

        /**
         * render the stats page
         * @updated 1.2.1
         */
        public function render_stats_page() {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            if (!empty($_POST['bacounter'])) {
                // reset statistics
                if ($_POST['bacounter'] == 'reset') {
                    $this->stat_method_standard_count_reset_statistics();
                }
                // load compare data
                if ($_POST['bacounter'] == 'compare') {
                    if ( $this->compare_allowed() ) {
                        require_once 'classes/tracking.php';
                        $this->_compare_data = BA_Tracking::compare();
                        $this->save_compare_data( $this->_compare_data );
                    }
                }
            }
            if ( empty( $this->_compare_data ) && get_option('ba_last_stats') ) {
                $this->_compare_data = get_option('ba_last_stats');
            }
            
            if ( $this->compare_allowed() ) {
                $this->_compare_allowed = true;
            } else {
                $this->_compare_allowed = false;
            }

            require_once 'templates/statistics.php';
        }

        /**
         * render settings section
         */
        public function render_settings_section() {
            ?><p><?php _e('You can choose one or more of the methods below to display the AdBlock statistics. If you disable all methods, measuring will be disabled.', BATD); ?></p><?php
        }

        /**
         * render the settings page
         * @since 1.1.2
         */
        public function render_settings_page() {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            ?><div id="icon-options-general" class="icon32"><br></div>
            <h2><?php _e('BlockAlyzer Settings', BATD); ?></h2>
            <div id="ba-admin-wrap">

                <form method="post" action="options.php">
                    <div class="postbox">
                        <?php
                        settings_fields('ba_settings_group');
                        // settings_fields('ba-settings-benchmark-section');
                        do_settings_sections('ba-settings-page');
                        ?>
                    </div>
                    
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', BATD); ?>">
                    </p>
                </form>
            </div><?php
        }

        /**
         * add scripts for the frontend
         */
        public function enqueue_scripts() {
            
            if ( !$this->_is_measuring ) return;
            // enqueue empty advertisement.js
            wp_register_script('adblock-counter-testjs', plugins_url('js/advertisement.js', __FILE__), array('jquery'), BAVERSION);
            wp_enqueue_script('adblock-counter-testjs');
            // add the ajax url for the frontend
            wp_localize_script('jquery', 'BaAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
        }

        /**
         * add scripts to admin panel
         */
        public function enqueue_admin_scripts() {
            wp_register_style('ba_admin_css', plugins_url('/css/admin-style.css', __FILE__), false, BAVERSION);
            wp_enqueue_style('ba_admin_css');
        }

        /**
         * create user id if not exists
         * @since 1.1
         */
        public function create_user_id() {
            if ( !$this->_is_measuring ) return;
            
            if (!empty($_COOKIE['BaUniqueVisitorId'])) {
                $this->_user_id = $_COOKIE['BaUniqueVisitorId'];
            } else {
                $this->_user_id = wp_create_nonce($_SERVER['REMOTE_ADDR']);
                $this->_is_new_user = true;
            }
        }

        /**
         * retrieve the current user id
         * @since 1.1
         */
        public function get_user_id() {
            if ( empty( $this->_user_id ) ) $this->create_user_id();
            echo $this->_user_id;
            wp_die();
        }

        /**
         * display a img-tag with gif banner
         * @since 1.1
         */
        public function include_bannergif() {
            if ( !$this->_is_measuring ) return;
            ?><img id = "ba_banner" src = "<?php echo plugins_url('/img/ads/banner.gif', __FILE__); ?>" alt = "banner" width = "1" height = "1" /><?php
        }
        
        /**
         * basic js functions that are needed
         * added to the header, because other plugins might need them earlier
         */
        public function head_js() {
            if ( !$this->_is_measuring ) return;
            ?><script type="text/javascript">//<![CDATA[
            function BaGetCookie(c_name) { var i,x,y,ARRcookies=document.cookie.split(";"); for (i=0;i<ARRcookies.length;i++) { x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("=")); y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1); x=x.replace(/^\s+|\s+$/g,""); if (x==c_name) { return unescape(y); } } }
            function BaSetCookie( name, value, exdays, path, domain, secure) { var exdate=new Date(); exdate.setDate(exdate.getDate() + exdays); document.cookie = name + "=" + escape(value) + ((exdate == null) ? "" : "; expires=" + exdate.toUTCString()) + ((path == null) ? "; path=/" : "; path=" + path) + ((domain == null) ? "" : "; domain=" + domain) +((secure == null) ? "" : "; secure");}
            //]]></script><?php
        }

        /**
         * content box that goes into the footer
         * @update 1.1
         */
        public function display_footer() {
            if ( !$this->_is_measuring ) return;
            ?><script type="text/javascript">//<![CDATA[
                jQuery(document).ready(function($) {
                    setTimeout(function(){ // timeout to run after loading the advertisement.js
                        // count for missing js file
                        var nonce = '<?php echo get_option('ba_nonce'); ?>';
                        <?php /* 
                         * set unique user id; currently not needed, but maybe for a later use
                        if ( !BaGetCookie('BaUniqueVisitorId') || BaGetCookie('BaUniqueVisitorId') == 0 ) {
                            var data = { action: 'get_user_id' };
                            $.post(BaAjax.ajaxurl, data, function(response) {
                                BaSetCookie('BaUniqueVisitorId', response, 30);
                            });
                        } */ ?>

                        var ba_blocked=false;
                        if ($.adblockJsFile === undefined){
                            ba_blocked=true;
                        }

                        var banner = document.getElementById("ba_banner");                        

                        if (banner == null || banner.offsetHeight == 0){
                            ba_blocked=true;
                        }

                        if(ba_blocked==true){	
                            BaSetCookie('BaAdBlock', 'enabled', 30);
                        }else{
                            BaSetCookie('BaAdBlock', 'disabled', 30);
                        }
                        <?php do_action('ba_js_footer'); ?>
                    },100);
                });
            //]]></script><?php
        }

        /**
         * run on activation of the plugin
         */
        public function _activation() {

            $this->_update_nonce();
            update_option('ba_last_reset', time() );
            update_option('ba_version', BAVERSION );
        }

        /**
         * update nonce using the current time
         * @return string $nonce nonce, if needed as a return
         */
        public function _update_nonce() {

            $nonce = wp_create_nonce(time());
            update_option('ba_nonce', $nonce);
            return $nonce;
        }
        
        // EVERYTHING NEEDED FOR STANDARD STATS METHOD
        
        /**
         * js code to use for the standard measurement method
         * @since 1.1.2
         */
        public function stat_method_standard_js() {
            if ( !in_array( 'basic', $this->_active_stat_methods ) ) return;
                ?>var data = {
                    action: 'standard_count',
                    blocked: ba_blocked
                };
                data.ba_count_jsFile = false;
                if ($.adblockJsFile === undefined){
                    data.ba_count_jsFile = true;
                }
                
                data.ba_count_banner = false;    
                if (banner == null || banner.offsetHeight == 0){
                    data.ba_count_banner = true;
                }

                $.post(BaAjax.ajaxurl, data, function(response) {
                    <?php /* if ( !AbcGetCookie('AbcUniqueVisitorJsFile') || AbcGetCookie('AbcUniqueVisitorJsFile') != nonce  ) {
                        AbcSetCookie('AbcUniqueVisitorJsFile', nonce, 30);
                    }     
                    if ( !AbcGetCookie('AbcUniqueVisitorBanner') || AbcGetCookie('AbcUniqueVisitorBanner') != nonce  ) {
                        AbcSetCookie('AbcUniqueVisitorBanner', nonce, 30);     
                    }     */ ?>
                    if ( !BaGetCookie('BaUniqueVisitor') || BaGetCookie('BaUniqueVisitor') != nonce ) {
                        BaSetCookie('BaUniqueVisitor', nonce, 30);    
                    }
            });<?php
        }
        
        /**
         * Should combine the total page views
         * @since 1.1
         */
        public function stat_method_standard_count() {

            $this->stat_method_standard_count_page_views();
            $this->stat_method_standard_count_unique_visitors();
            
            if (isset($_POST['blocked']) && $_POST['blocked'] == "true") {
                $this->stat_method_standard_count_blocked_page_views();
                $this->stat_method_standard_count_blocked_unique_visitors();
            }
            
            /* if (isset($_POST['abc_count_jsFile']) && $_POST['abc_count_jsFile'] == "true") {
                $this->stat_method_standard_count_jsFile();
            }
            if (isset($_POST['abc_count_banner']) && $_POST['abc_count_banner'] == "true") {
                $this->stat_method_standard_count_banner();
            } */
            wp_die();
        }
        
        /**
         * count the total page views
         * @update 1.1
         */
        public function stat_method_standard_count_page_views() {

            $page_views = get_option('ba_page_views', 0);
            $page_views++;
            update_option('ba_page_views', $page_views);

            //wp_die();
        }
        /**
         * count the ad blocked page views
         * @since 1.2
         */
        public function stat_method_standard_count_blocked_page_views() {

            $page_views = get_option('ba_page_views_blocked', 0);
            $page_views++;
            update_option('ba_page_views_blocked', $page_views);

            //wp_die();
        }

        /**
         * count the total page views
         * use the nonce to identify the visitor
         * @update 1.1
         */
        public function stat_method_standard_count_unique_visitors() {

            if (!empty($_COOKIE['BaUniqueVisitor']) && $_COOKIE['BaUniqueVisitor'] == get_option('ba_nonce'))
                return;

            $uniques = get_option('ba_unique_visitors', 0);
            $uniques++;
            update_option('ba_unique_visitors', $uniques);
        }

        /**
         * count the blocked page views
         * @since 1.2
         */
        public function stat_method_standard_count_blocked_unique_visitors() {

            if (!empty($_COOKIE['BaUniqueVisitor']) && $_COOKIE['BaUniqueVisitor'] == get_option('ba_nonce'))
                return;

            $uniques = get_option('ba_unique_visitors_blocked', 0);
            $uniques++;
            update_option('ba_unique_visitors_blocked', $uniques);
        }

        /**
         * count when advertisement.js is missing
         * @update 1.1
         * deprecated since 1.2
         */
        public function stat_method_standard_count_jsFile() {

            $count = get_option('ba_page_views_jsFile', 0);
            $count++;
            update_option('ba_page_views_jsFile', $count);

            // only count, if wasn't count before or nonce was reset
            if (empty($_COOKIE['BaUniqueVisitorJsFile']) || $_COOKIE['BaUniqueVisitorJsFile'] != get_option('ba_nonce')) {

                $uniques = get_option('ba_unique_visitors_jsFile', 0);
                $uniques++;
                update_option('ba_unique_visitors_jsFile', $uniques);
            }
        }

        /**
         * count when banner is missing
         * @since 1.1
         * deprecated since 1.2
         */
        public function stat_method_standard_count_banner() {
            $count = get_option('ba_page_views_bannerFile', 0);
            $count++;
            update_option('ba_page_views_bannerFile', $count);

            // only count, if wasn't count before or nonce was reset
            if (empty($_COOKIE['BaUniqueVisitorBanner']) || $_COOKIE['BaUniqueVisitorBanner'] != get_option('ba_nonce')) {

                $uniques = get_option('ba_unique_visitors_bannerFile', 0);
                $uniques++;
                update_option('ba_unique_visitors_bannerFile', $uniques);
            }
        }       
        
        /**
         * reset the statistics to 0
         * @updated 1.2
         */
        public function stat_method_standard_count_reset_statistics() {

            update_option('ba_page_views', 0);
            update_option('ba_page_views_blocked', 0);
            update_option('ba_unique_visitors', 0);            
            update_option('ba_unique_visitors_blocked', 0);
            update_option('ba_last_reset', time() );

            $this->_update_nonce();
        }        
        
        /**
         * check, if comparing data is allowed
         * conditions:
         * * data is at least 24 hours old
         * * at least 20 visits and views
         * * at least 1 visit and view with AdBlock
         * @since 1.2
         */
        public function compare_allowed() {
            // timestamp from one day ago
            $min_time = strtotime('-1 day', time());
            // timestamp from 12 hours ago
            $min_send_again_time = strtotime('-3 hours', time());
            // check if measuring time is at least 24 hours
            if ( $min_time < intval ( get_option('ba_last_reset', 0)) ) return false;
            if ( get_option('ba_last_sent', 0) && $min_send_again_time < intval ( get_option('ba_last_sent', time())) ) return false;
            if ( 20 >   intval ( get_option('ba_page_views', 0))) return false;
            if ( 1  >   intval ( get_option('ba_page_views', 0))) return false;
            if ( 20 >   intval ( get_option('ba_page_views', 0))) return false;
            if ( 1  >   intval ( get_option('ba_page_views', 0))) return false;
            return true;
        }
        
        /**
         * save the compare data
         * @since 1.2.1
         */
        public function save_compare_data( $data ) {
            
            if ( empty( $data->general->totalViews )) return;
            
            update_option( 'ba_last_stats', $data );
            
        }
        
        /**
         * upgrade script
         */
        public function upgrade() {
            $version = get_option( 'ba_version', 0 );
            if ( 0 == version_compare( $version, BAVERSION )) return;
            // prior to version 1.2.2
            // convert all stats and options to new fields
            if ( empty( $version ) ) {
                update_option( 'ba_last_stats', get_option('abc_last_stats') );
                delete_option( 'abc_last_stats' );
                update_option( 'ba_tracking_hash', get_option('abc_tracking_hash') );
                delete_option( 'abc_tracking_hash' );
                update_option( 'ba_page_views', get_option('abc_page_views') );
                delete_option( 'abc_page_views' );
                update_option( 'ba_unique_visitors', get_option('abc_unique_visitors') );
                delete_option( 'abc_unique_visitors' );
                update_option( 'ba_page_views_blocked', get_option('abc_page_views_blocked') );
                delete_option( 'abc_page_views_blocked' );
                update_option( 'ba_unique_visitors_blocked', get_option('abc_unique_visitors_blocked') );
                delete_option( 'abc_unique_visitors_blocked' );
                update_option( 'ba_last_sent', get_option('abc_last_sent') );
                delete_option( 'abc_last_sent' );
                update_option( 'ba_last_reset', get_option('abc_last_reset') );
                delete_option( 'abc_last_reset' );
                update_option( 'ba_methods', get_option('abc_methods') );
                delete_option( 'abc_methods' );
            }
            // run this, if there is a new version
            if ( !empty( $version ) && -1 == version_compare($version, '1.2.3') ) {
                $stats = get_option('ba_last_stats', true);
                if ( !empty( $stats ) ) {
                    $new_stats->general = $stats;
                    update_option('ba_last_stats', $new_stats);
                }
                $options = array(
                    'methods' => get_option('ba_methods'),
                    'ba_benchmark_category' => ge_option('ba_benchmark_category')
                );
                update_option('ba_settings', $array );
                delete_option('ba_methods');
                delete_option('ba_benchmark_category');
            }
            update_option( 'ba_version', BAVERSION );
        }
        
        /**
         * return benchmark site categories
         * @since 1.2.3
         */
        public function get_site_categories () {
            
            if ( empty( $this->_site_categories ) ) {
                require_once( BAPATH . 'inc/site_categories.php');
                if ( empty( $site_categories ) ) return;
                $this->_site_categories = $site_categories;
            }
            return $this->_site_categories;
            
        }
        
    }

    $blockalyzer = new BA_CLASS();
}
