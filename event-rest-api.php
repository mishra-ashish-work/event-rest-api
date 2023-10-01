<?php
/**
 * Plugin Name: Event Rest API
 * Description: Event Rest API plugin to perform CRUD Operations on events.
 * Author: Ashish Mishra
 * Author URI: https://www.storeapps.org/
 * Version: 1.0
 * Requires at least: 6
 * Requires PHP: 7.4
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: event-rest-api
 */

// Define Constants

/**
 * Plugin Version
 * @since 1.0
 */
define('ERA_VERSION', '1.0');

/**
 * Text Domain for Translation
 * @since 1.0
 */
define('ERA_TEXT_DOMAIN', 'event-rest-api');

/**
 * API Endpoint for Event Rest API
 * @since 1.0
 */
define('ERA_ENDPOINT', '/era/v1/');

/**
 * Base slug for Event Post Type
 * @since 1.0
 */
define('ERA_EVENT_BASE', 'events');

// Include Core Files

/**
 * Include the core-import.php file for essential functionality.
 * @since 1.0
 */
include 'inc/core-import.php';

// Register the Post Type and Taxonomy

/**
 * Initialize the Event Custom Post Type (CPT) and Event Category Taxonomy
 * @since 1.0
 */
$events = new ERA_CPT('events', 'event_cat');
