<?php 
class ERA_REST_Helper{
    public function __construct(){
        add_filter( 'rest_authentication_errors', array($this, 'json_basic_auth_error') );
        add_filter( 'determine_current_user', array($this, 'json_basic_auth_handler'), 20 );
    }

    public function json_basic_auth_handler( $user ) {
        global $wp_json_basic_auth_error;
    
        $wp_json_basic_auth_error = null;
    
        // Don't authenticate twice
        if ( ! empty( $user ) ) {
            return $user;
        }
    
        // Check that we're trying to authenticate
        if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
            return $user;
        }
    
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
    
        /**
         * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
         * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
         * recursion and a stack overflow unless the current function is removed from the determine_current_user
         * filter during authentication.
         */
        remove_filter( 'determine_current_user', array($this, 'json_basic_auth_handler'), 20 );
    
        $user = wp_authenticate( $username, $password );
    
        add_filter( 'determine_current_user', array($this, 'json_basic_auth_handler'), 20 );
    
        if ( is_wp_error( $user ) ) {
            $wp_json_basic_auth_error = $user;
            return null;
        }
    
        $wp_json_basic_auth_error = true;
    
        return $user->ID;
    }

    public function json_basic_auth_error( $error ) {
        // Passthrough other errors
        if ( ! empty( $error ) ) {
            return new WP_Error(
                'rest_error',
                __( 'A fatal error occured.' ),
                array( 'status' => 500 )
            );
        }

        if ( !current_user_can( 'administrator' ) ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'Sorry, you do not have permission to access the REST API.' ),
                array( 'status' => 401 )
            );
        }
    
        global $wp_json_basic_auth_error;
    
        return $wp_json_basic_auth_error;
    }
}

new ERA_REST_Helper();
?>
