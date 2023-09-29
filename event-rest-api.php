<?php 
/**
 * Plugin Name: Event Rest API
 * Description: Event Rest API plugin to perform CURD Operations.
 * Author: Ashish Mishra
 * Author URI: https://www.storeapps.org/
 * Version: 1.0
 * Requires at least: 6
 * Requires PHP: 7.4
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: event-rest-api
 */


define('ERA_VERSION', '1.0');
define('ERA_TEXT_DOMAIN', 'event-rest-api');
define('ERA_ENDPOINT', 'era/v1/events');

include 'inc/core-import.php';

//register the post type and tax
$events = new ERA_CPT('events', 'event_cat');
?>