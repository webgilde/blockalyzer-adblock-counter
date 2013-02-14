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
        }

        /**
         * add menu page in tools section
         */
        public function add_menu_page(){
            add_management_page( __('AdBlock Counter Dashboard', ABCOUNTERTD), __('AdBlock Counter', ABCOUNTERTD), 'manage_options', 'adblock-counter', array($this, 'render_menu_page') );
        }
        
        /**
         * render the menu page
         */
        public function render_menu_page(){
            
        }
        
        /**
         * add scripts
         */
        public function enqueue_scripts() {
            // enqueue empty advertisement.js
            wp_register_script('adblock-counter-testjs', plugins_url('js/advertisement.js', __FILE__), array('jquery'), ABCOUNTERVERSION);
            wp_enqueue_script('adblock-counter-testjs');
        }

        /**
         * content box that goes into the footer
         */
        public function display_footer() {
            ?><script>
                jQuery(document).ready(function($) {
                    console.log( $.adblockJsFile );
                    if ($.adblockJsFile === undefined){
                        
                    }
                });
            </script><?php
        }

    }

    $adblock_counter = new ABCOUNTER_CLASS();
}