<?php

if ( !defined( 'ABCOUNTER_CLASS' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * compare and track adblock stats
 * @since 1.2

 */
if ( !class_exists( 'ABC_Tracking' ) ) {
    class ABC_Tracking {
        
        /**
         * send blog adblock data
         */
        public function send() {
            
            $data = array(
                'site' => array(
                    'url'       => site_url(),
                    'name'      => get_bloginfo( 'name' ),
                    'lang'      => get_locale(),
                ),                
                'stats' => array(
                    'views'     => get_option('abc_page_views', 0),
                    'users'     => get_option('abc_unique_visitors', 0)
                )
            );
            
            $args = array(
                'body' => $data
            );
            wp_remote_post( 'https://abstats.webgilde.com/', $args );
            
        }
        
        
        
    }
}