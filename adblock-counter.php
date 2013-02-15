<?php
/*
  Plugin Name: Adblock Counter
  Version: 1.0.0
  Plugin URI: http://webgilde.com/
  Description: Count how many of your visitors are using an ad blocker.
  Author: Thomas Maier
  Author URI: http://www.webgilde.com/
  License: GPL v3

  adblock-counter Plugin for WordPress
  Copyright (C) 2013, Thomas Maier (thomas.maier@webgilde.com)

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
 * @todo combine this plugin with ads optimizer
 * @todo use this method for specific blocks (of there is the same ad block on each page): http://pastebin.com/QdJEpR8K
 * @todo add a method that tries to include a banner image (1px x 1px) like /ad/banner.gif and than check height of this field
 * 
 */

//avoid direct calls to this file
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('ABCOUNTERVERSION', '1.0.0');
define('ABCOUNTERNAME', 'adblock-counter');
define('ABCOUNTERTD', 'adblock-counter');
define('ABCOUNTERDIR', basename(dirname(__FILE__)));
define('ABCOUNTERPATH', plugin_dir_path(__FILE__));

if (!class_exists('ABCOUNTER_CLASS')) {

    class ABCOUNTER_CLASS {

        /**
         * initialize the plugin
         */
        public function __construct() {

            add_action('admin_menu', array($this, 'add_menu_page'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_footer', array($this, 'display_footer'));
            add_action('shutdown', array($this, 'count_page_views'));
            add_action('shutdown', array($this, 'count_unique_visitors'));
            // ajax call for logged in and not logged in users
            add_action('wp_ajax_abc_count_jsFile', array($this, 'count_jsFile'));
            add_action('wp_ajax_nopriv_abc_count_jsFile', array($this, 'count_jsFile'));
            // load admin scripts
            add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts'));
        }

        /**
         * add menu page in tools section
         */
        public function add_menu_page() {
            add_management_page(__('AdBlock Counter Dashboard', ABCOUNTERTD), __('AdBlock Counter', ABCOUNTERTD), 'manage_options', 'adblock-counter', array($this, 'render_menu_page'));
        }

        /**
         * render the menu page
         */
        public function render_menu_page() {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            
            if (!empty( $_POST['abcounter'] ) ) {
                // reset statistics
                if ( $_POST['abcounter']['reset'] == 'reset' ) {
                    $this->_reset_statistics();
                }
            }

            require_once 'templates/settings.php';
            require_once 'templates/statistics.php';
        }

        /**
         * add scripts for the frontend
         */
        public function enqueue_scripts() {
            // enqueue empty advertisement.js
            wp_register_script('adblock-counter-testjs', plugins_url('js/advertisement.js', __FILE__), array('jquery'), ABCOUNTERVERSION);
            wp_enqueue_script('adblock-counter-testjs');
            // add the ajax url for the frontend
            wp_localize_script('jquery', 'AbcAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
        }

        /**
         * add scripts to admin panel
         */
        public function enqueue_admin_scripts() {
            wp_register_style('abc_admin_css', plugins_url('/css/admin-style.css', __FILE__), false, ABCOUNTERVERSION);
            wp_enqueue_style('abc_admin_css');
        }

        /**
         * content box that goes into the footer
         */
        public function display_footer() {
            ?><script>
                            jQuery(document).ready(function($) {
                                // count for missing js file
                                if ($.adblockJsFile === undefined){
                                    var data = {
                                        action: 'abc_count_jsFile'
                                    };
                                    $.post(AbcAjax.ajaxurl, data, function(response) {
                                        if ( !AbcGetCookie('AbcUniqueVisitorJsFile') ) {
                                            AbcSetCookie('AbcUniqueVisitorJsFile', 1, 30);     
                                        }                                        
                                    }); 
                                }
                                if ( !AbcGetCookie('AbcUniqueVisitor') ) {
                                    AbcSetCookie('AbcUniqueVisitor', 1, 30);     
                                }
                                                                                                        
                            });
                            function AbcGetCookie(c_name)
                            {
                                var i,x,y,ARRcookies=document.cookie.split(";");
                                for (i=0;i<ARRcookies.length;i++)
                                {
                                    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                                    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                                    x=x.replace(/^\s+|\s+$/g,"");
                                    if (x==c_name)
                                    {
                                        return unescape(y);
                                    }
                                }
                            }

                            /**
                             * name = cookie name
                             * value = cookie value
                             * exdays = days until cookie expires
                             */
                            function AbcSetCookie( name, value, exdays, path, domain, secure)
                            {
                                var exdate=new Date();
                                exdate.setDate(exdate.getDate() + exdays);
                                document.cookie = name + "=" + escape(value) + 
                                    ((exdate == null) ? "" : "; expires=" + exdate.toUTCString()) +
                                    ((path == null) ? "; path=/" : "; path=" + path) +        
                                    ((domain == null) ? "" : "; domain=" + domain) +
                                    ((secure == null) ? "" : "; secure");
                            }

            </script><?php
        }

        /**
         * count the total page views
         */
        public function count_page_views() {

            if (is_admin())
                return;

            $page_views = get_option('abc_page_views', 0);
            $page_views++;
            update_option('abc_page_views', $page_views);
        }

        /**
         * count the total page views
         */
        public function count_unique_visitors() {

            if (is_admin())
                return;
            if (!empty($_COOKIE['AbcUniqueVisitor']))
                return;

            $uniques = get_option('abc_unique_visitors', 0);
            $uniques++;
            update_option('abc_unique_visitors', $uniques);
        }

        /**
         * count when advertisement.js is missing
         */
        public function count_jsFile() {

            $count = get_option('abc_page_views_jsFile', 0);
            $count++;
            update_option('abc_page_views_jsFile', $count);

            if (empty($_COOKIE['AbcUniqueVisitorJsFile'])) {

                $uniques = get_option('abc_unique_visitors_jsFile', 0);
                $uniques++;
                update_option('abc_unique_visitors_jsFile', $uniques);
            }

            wp_die();
        }
        
        /**
         * reset the statistics to 0
         */
        public function _reset_statistics(){
            
            update_option('abc_page_views', 0);
            update_option('abc_unique_visitors', 0);
            update_option('abc_page_views_jsFile', 0);
            update_option('abc_unique_visitors_jsFile', 0);
            
        }

    }

    $adblock_counter = new ABCOUNTER_CLASS();
}