<?php
/**
 * Package: Event Rest API
 * Class: Event Rest
 * Description: Class to control all the rest api development.
 */

 class ERA_EVENT_REST{
    public function __construct(){
      add_action( 'rest_api_init', array($this, 'era_regsiter_endpoint'));
    }

    public function era_regsiter_endpoint(){
      register_rest_route(
         ERA_ENDPOINT,
         '/list',
         array(
             'methods' => 'GET',
             'callback' => [$this, 'get_response']
         )
     );
    }

    public function get_response(){
      return "hello world";
    }
 }

 new ERA_EVENT_REST();
?>