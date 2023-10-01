<?php
/**
 * Package: Event Rest API
 * Class: Event Rest
 * Description: This class is responsible for controlling all REST API development related to events.
 */

class ERA_EVENT_REST extends WP_REST_Controller {

  /**
   * Constructor for the ERA_EVENT_REST class.
   * It initializes and registers the REST API routes.
   */
  public function __construct(){
    add_action('rest_api_init', array($this, 'era_register_routes'));
  }

  /**
   * Register REST API routes for Event Rest API.
   */
  public function era_register_routes(){
    // Register route to list events.
    register_rest_route(ERA_ENDPOINT, '/'.ERA_EVENT_BASE.'/list', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => [$this, 'era_get_events']
      ),
    );

    // Register route to show an event by ID.
    register_rest_route(ERA_ENDPOINT, '/'.ERA_EVENT_BASE.'/show', array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => [$this, 'era_show_event'],
      'args' => array(
        'id' => array(
          'validate_callback' => function($param, $request, $key) {
            return is_numeric( $param );
          }
        ),
      ),
    ),
    );

    // Register route to create an event.
    register_rest_route(ERA_ENDPOINT, '/' . ERA_EVENT_BASE . '/create', array(
      'methods'             => WP_REST_Server::CREATABLE,
      'callback'            => array($this, 'era_create_event'),
      'permission_callback' => array($this, 'era_events_permissions_check'),
      'args'                => $this->get_endpoint_args_for_item_schema(true),
    ));

    // Register route to update an event.
    register_rest_route(ERA_ENDPOINT, '/' . ERA_EVENT_BASE . '/update', array(
      'methods'             => WP_REST_Server::EDITABLE,
      'callback'            => array($this, 'era_update_event'),
      'permission_callback' => array($this, 'era_events_permissions_check'),
      'args'                => $this->get_endpoint_args_for_item_schema(true),
    ));

    // Register route to delete an event.
    register_rest_route(ERA_ENDPOINT, '/' . ERA_EVENT_BASE . '/delete', array(
      'methods'             => WP_REST_Server::DELETABLE,
      'callback'            => array($this, 'era_delete_event'),
      'permission_callback' => array($this, 'era_events_permissions_check'),
      'args'                => array('force' => array('default' => false,),),
    ));
  }

  /**
   * Callback function to get events based on provided criteria.
   *
   * @param WP_REST_Request $request The REST API request object.
   * @return WP_REST_Response|WP_Error The response with events or an error.
   */
  public function era_get_events(WP_REST_Request $request){
    if(!empty($request->get_params())){

      $era_event_args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'posts_per_page' => -1,
      );

      $date_param = $request->get_param('date');
      $datebtw_param = $request->get_param('datebtw');
      $cat_param = $request->get_param('cat');
      $title_param = $request->get_param('title');

      //Queries setup
      $era_tax_query = [];
      $era_meta_query = [];

      //Date Parameter
      if($date_param){
        $parsed_date = date_parse($date_param);
        if ($parsed_date !== false && $parsed_date['error_count'] === 0) {
            $era_meta_query[] = [
              'key' => '_event_start_date',
              'value' => date('Y-m-d\TH:i:s', strtotime($date_param)),
              'compare' => '>=',
              'type' => 'DATETIME'
            ];
        } else {
            return new WP_Error('invalid_date', 'Invalid date parameter.', array('status' => 400));
        }
      }

      //Date Between Parameter
      if($datebtw_param){
        list($datebtw_start, $datebtw_end) = explode('TO', $datebtw_param);
        
        $parse_startDate = date_parse($datebtw_start);
        $parse_endDate = date_parse($datebtw_end);

        if(($parse_startDate !== false && $parse_startDate['error_count'] === 0) 
          && ($parse_endDate !== false && $parse_endDate['error_count'] === 0)){
            $era_meta_query[] = [
              'relation' => 'AND',
              [
                'key'     => '_event_start_date',
                'value' => date('Y-m-d\TH:i:s', strtotime($datebtw_start)),
                'compare' => '>=',
                'type' => 'DATE'
              ],
              [
                'key'     => '_event_end_date',
                'value' => date('Y-m-d\TH:i:s', strtotime($datebtw_end)),
                'compare' => '<=',
                'type' => 'DATE'
              ]
            ];
        } else {
            return new WP_Error('invalid_date', 'Invalid dateBtw parameter.', array('status' => 400));
        }
      }

      //Category Param
      if($cat_param){
        $era_event_cat = get_term_by('slug', $cat_param, 'event_cat');
        if(!empty($era_event_cat)){
          $era_tax_query[] = array(
            'taxonomy' => 'event_cat',
            'field' => 'slug',
            'terms' => $era_event_cat,
          );
        }else{
          return new WP_Error('invalid_category_slug', 'Category Slug Parameter for Event not Found', array('status' => 404));
        }
        
      }

      //Title Like param
      if($title_param){
        $era_event_args['s'] = $title_param;
      }

      //set meta query
      $era_event_args['meta_query'] = $era_meta_query;

      //set tax query
      $era_event_args['tax_query'] = $era_tax_query;
      print_r($era_event_args);
      $era_posts = new WP_Query($era_event_args);
      if ( $era_posts->have_posts() ) {
        $era_events = [];
        while ( $era_posts->have_posts() ) {
          $era_posts->the_post(); 
          $era_events[] = array(
            'title' => get_the_title(),
            'description' => get_the_content(),
            'start' => get_post_meta(get_the_ID(), '_event_start_date', true),
            'end' => get_post_meta(get_the_ID(), '_event_end_date', true),
          );
        }

        return new WP_REST_Response( $era_events, 200 );
      }else{
        return new WP_Error('not_found', 'No Events Found on Provided Criteria', array('status' => 404));
      }
      
    }else{
      $era_event_args = array(
        'post_type' => 'events',
        'post_status' => 'publish',
        'posts_per_page' => -1,
      );

      $era_posts = new WP_Query($era_event_args);
      if ( $era_posts->have_posts() ) {
        $era_events = [];
        while ( $era_posts->have_posts() ) {
          $era_posts->the_post(); 
          $era_events[] = array(
            'title' => get_the_title(),
            'description' => get_the_content(),
            'start' => get_post_meta(get_the_ID(), '_event_start_date', true),
            'end' => get_post_meta(get_the_ID(), '_event_end_date', true),
          );
        }
        return new WP_REST_Response( $era_events, 200 );
      }else{
        return new WP_Error('not_found', 'No Events Found', array('status' => 404));
      }
    }
  }

  /**
   * Callback function to create an event.
   *
   * @param WP_REST_Request $request The REST API request object.
   * @return WP_REST_Response|WP_Error The response with the created event or an error.
   */
  public function era_create_event(WP_REST_Request $request){
    //Category Validation
    $era_term = $request->get_param('category');
    if($era_term){
      $era_term_exists = term_exists($era_term, 'event_cat');
      if ($era_term_exists === 0 || $era_term_exists === null) {
        $era_term_args = array(
            'name' => $era_term,
            'slug' => sanitize_title($era_term),
            'description' => '',
            'parent' => 0,
        );

        $era_term_arr = wp_insert_term($era_term, 'event_cat', $era_term_args);
        $era_event_term = $era_term_arr['term_id'];
      }else{
        $era_event_term = $era_term_exists['term_id'];
      }
    }
    
    //Date Validations
    $era_start_date = $request->get_param('start');
    if($era_start_date){
      $parse_startDate = date_parse($era_start_date);
      if(($parse_startDate !== false && $parse_startDate['error_count'] === 0)){
        $era_args_start_date = date('Y-m-d\TH:i:s', strtotime($era_start_date));
      } else {
          return new WP_Error('invalid_date', 'Invalid start date parameter.', array('status' => 400));
      }
    }

    $era_end_date = $request->get_param('end');
    if($era_end_date){
      $parse_endDate = date_parse($era_end_date);
      if(($parse_endDate !== false && $parse_endDate['error_count'] === 0)){
        $era_args_end_date = date('Y-m-d\TH:i:s', strtotime($era_end_date));
      } else {
          return new WP_Error('invalid_date', 'Invalid start date parameter.', array('status' => 400));
      }
    }

    //description
    if(!$request->get_param('description')){
      return new WP_Error('content_error', 'Content Cannot be Empty', array('status' => 404));
    }
    
    
    $era_event_args = [
      'post_type' => 'events',
      'post_title' => $request->get_param('title'),
      'post_slug' => sanitize_title($request->get_param('title')),
      'post_status' => 'publish',
      'post_content' => $request->get_param('description'),
      'post_author' => 1,
      'meta_query' => [
        '_event_start_date' => $era_args_start_date,
        '_event_end_date' => $era_args_end_date
      ],
      'tax_query' => [
        'event_cat' => $era_event_term,
      ]
    ];

    $era_event = wp_insert_post($era_event_args, true);
    if($wp_error){
      return new WP_Error('event_creation_error', $wp_error, array('status' => 500));
    }
    return new WP_REST_Response( $era_event, 200 );
  }

  /**
   * Callback function to update an event.
   *
   * @param WP_REST_Request $request The REST API request object.
   * @return WP_REST_Response|WP_Error The response with the updated event or an error.
   */
  public function era_update_event(WP_REST_Request $request){
    //ID Check
    $era_event_id = intval($request->get_param('id'));
    if ( get_post_type($era_event_id) === 'events' ) {
      $eventID = intval($request->get_param('id'));
      $era_event_args = [];
      if($request->get_param('title')){
        $era_event_args['post_title'] = $request->get_param('title');
      }

      if(!$request->get_param('description')){
        $era_event_args['post_content'] = $request->get_param('description');
      }

      $era_start_date = $request->get_param('start');
      if($era_start_date){
        $parse_startDate = date_parse($era_start_date);
        if(($parse_startDate !== false && $parse_startDate['error_count'] === 0)){
          $era_args_start_date = date('Y-m-d\TH:i:s', strtotime($era_start_date));
          update_post_meta($eventID, '_event_start_date', $era_args_start_date);
        } else {
            return new WP_Error('invalid_date', 'Invalid start date parameter.', array('status' => 400));
        }
      }

      $era_end_date = $request->get_param('end');
      if($era_end_date){
        $parse_endDate = date_parse($era_end_date);
        if(($parse_endDate !== false && $parse_endDate['error_count'] === 0)){
          $era_args_end_date = date('Y-m-d\TH:i:s', strtotime($era_end_date));
          update_post_meta($eventID, '_event_end_date', $era_args_end_date);
        } else {
            return new WP_Error('invalid_date', 'Invalid start date parameter.', array('status' => 400));
        }
      }

      //Category Validation
      $era_term = $request->get_param('category');
      if($era_term){
        $era_term_exists = term_exists($era_term, 'event_cat');
        if ($era_term_exists === 0 || $era_term_exists === null) {
          $era_term_args = array(
              'name' => $era_term,
              'slug' => sanitize_title($era_term),
              'description' => '',
              'parent' => 0,
          );

          $era_term_arr = wp_insert_term($era_term, 'event_cat', $era_term_args);
          $era_event_term = $era_term_arr['term_id'];

          wp_set_object_terms( $eventID, intval($era_event_term), 'event_cat' ); 
        }else{
          $era_event_term = $era_term_exists['term_id'];
          wp_set_object_terms( $eventID, intval($era_event_term), 'event_cat' ); 
        }
      }
      $era_event_args['ID'] = $eventID;

      $era_event_update = wp_update_post($era_event_args);
      return new WP_REST_Response( $era_event_update, 200 );
    }else{
      return new WP_Error('event_not_found', "Event Not Found", array('status' => 500));
    }
  }

  /**
   * Callback function to delete an event.
   *
   * @param WP_REST_Request $request The REST API request object.
   * @return WP_REST_Response|WP_Error The response indicating success or an error.
   */
  public function era_delete_event(WP_REST_Request $request){
    $era_event_id = $request->get_param('id');
    if ( get_post_type($era_event_id) === 'events' ) {
      $eventID = intval($request->get_param('id'));
      if(wp_delete_post( $eventID, false)){
        return new WP_REST_Response( "Event Deleted!!!", 200 );
      }else{
        return new WP_Error('event_delete', "Event Not Deleted", array('status' => 500));
      }
    }else{
      return new WP_Error('event_not_found', "Event Not Found", array('status' => 500));
    }
  }

  /**
   * Callback function to show an event.
   *
   * @param WP_REST_Request $request The REST API request object.
   * @return WP_REST_Response|WP_Error The response indicating success or an error.
   */
  public function era_show_event(WP_REST_Request $request){
    $era_event_id = $request->get_param('id');
    if ( get_post_type($era_event_id) === 'events' ) {
      $eventID = intval($request->get_param('id'));
      $era_events = [];
      $era_events[] = array(
        'title' => get_the_title($eventID),
        'description' => get_post_field('post_content', $eventID),
        'start' => get_post_meta($eventID, '_event_start_date', true),
        'end' => get_post_meta($eventID, '_event_end_date', true),
      );

      return new WP_REST_Response( $era_events, 200 );
    }else{
      return new WP_Error('event_not_found', "Event Not Found", array('status' => 500));
    }
  }

  /**
   * Callback function to check permissions for event operations.
   *
   * @return bool Whether the current user has permission (administrator).
   */
  public function era_events_permissions_check(){
    return current_user_can('administrator');
  }
    
}

// Create an instance of the ERA_EVENT_REST class.
$era_event_controller = new ERA_EVENT_REST();
?>
