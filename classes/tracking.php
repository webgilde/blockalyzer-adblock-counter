<?php

if (!class_exists('BA_CLASS')) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

/**
 * compare and track adblock stats
 * @since 1.2

 */
if (!class_exists('BA_Tracking')) {

    class BA_Tracking {

        /**
         * send blog adblock data and compare with data from others
         */
        static function compare() {

            $hash = get_option('ba_tracking_hash');

            if (empty($hash)) {
                $hash = md5( site_url() );
                update_option('ba_tracking_hash', $hash);
            }

            $data = array(
                'site' => array(
                    'hash' => $hash,
                    'url' => site_url(),
                    'topic' => '',
                    'name' => '', //get_bloginfo('name'),
                    'lang' => get_locale(),
                    'country' => '',
                    'category' => get_option('ba_benchmark_category'),
                ),
                'stats' => array(
                    'last_reset' => get_option('ba_last_reset', 0),
                    'total_views' => get_option('ba_page_views', 0),
                    'unique_users' => get_option('ba_unique_visitors', 0),
                    'views_blocked' => get_option('ba_page_views_blocked', 0),
                    'users_blocked' => get_option('ba_unique_visitors_blocked', 0)
                )
            );

            $args = array(
                'body' => $data
            );
            $result = wp_remote_post('http://stats.blockalyzer.com/', $args);

            if (200 == $result['response']['code']) {

                $return = json_decode($result['body']);
                
                if ( !empty( $return->errors ) && is_array( $return->errors)) {
                    self::render_errors( $return->errors );
                } else {
                    update_option('ba_last_sent', time());
                    return $return;
                }
                
            } else {
                self::render_server_error();
            }
            
            return false;
        }
        
        /**
         * show message for server error
         */
        public function render_server_error(){
            
            ?><div class="error"><p>Server error. Please try again later. If there are no changed, please contact the plugin author.</p></div><?php
            
        }

        /**
         * show error messages
         */
        public function render_errors( $errors ){
            
            if ( !empty( $errors ) && is_array( $errors ) && count( $errors) > 0 ) {
            ?><div class="error"><ul><?php
                foreach( $errors as $_error ) : 
                    ?><li><?php echo $_error; ?></li><?php 
                endforeach;
            ?></ul></div><?php
            }
            
        }
        
        /**
         * return benchmark site categories
         * @since 1.2.3
         */
        public function get_site_categories () {
            require_once( BAPATH . 'inc/site_categories.php');
            if ( empty( $site_categories ) ) return;
            
            return $site_categories;
            
        }

    }

}