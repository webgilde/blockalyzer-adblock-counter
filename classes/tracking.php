<?php

if (!class_exists('ABCOUNTER_CLASS')) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

/**
 * compare and track adblock stats
 * @since 1.2

 */
if (!class_exists('ABC_Tracking')) {

    class ABC_Tracking {

        /**
         * send blog adblock data
         */
        static function send() {

            $hash = get_option('abc_tracking_hash');

            if (empty($hash)) {
                $hash = md5( site_url() );
                update_option('abc_tracking_hash', $hash);
            }

            $data = array(
                'site' => array(
                    'hash' => $hash,
                    'url' => site_url(),
                    'topic' => '',
                    'name' => get_bloginfo('name'),
                    'lang' => get_locale(),
                    'country' => '',
                ),
                'stats' => array(
                    'last_reset' => get_option('abc_last_reset', 0),
                    'total_views' => get_option('abc_page_views', 0),
                    'unique_users' => get_option('abc_unique_visitors', 0),
                    'views_blocked' => get_option('abc_page_views_blocked', 0),
                    'users_blocked' => get_option('abc_unique_visitors_blocked', 0)
                )
            );

            $args = array(
                'body' => $data
            );
            $result = wp_remote_post('http://stats.blockalyzer.com/', $args);
            //print_r( $result );

            if (200 == $result['response']['code']) {

                $return = json_decode($result['body']);
              //  print_r($return->errors );
                
                if ( !empty( $return->errors ) && is_array( $return->errors)) {
                    self::render_errors( $return->errors );
                }
                
            } else {
                self::render_server_error();
            }
        }
        
        /**
         * show message for server error
         */
        public function render_server_error(){
            
            ?><p class="error">Server error. Please try again later. If there are no changed, please contact the plugin author.</p><?php
            
        }

        /**
         * show error messages
         */
        public function render_errors( $errors ){
            
            if ( !empty( $errors ) && is_array( $errors ) && count( $errors) > 0 ) {
            ?><ul class="error"><?php
                foreach( $errors as $_error ) : 
                    ?><li><?php echo $_error; ?></li><?php 
                endforeach;
            ?></ul><?php
            }
            
        }

    }

}