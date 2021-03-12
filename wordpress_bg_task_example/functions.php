<?php

require_once 'background-processing.php';

/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

//group by month and year
add_shortcode('heading', 'my_heading');
 
function my_heading($atts, $content = '') {
  static $year = null;
  static $month = null;
  $condition = $atts['condition'];
  $value = $atts['value'];
  switch ($condition) {
    case 'year':
    case 'month':
      if ($$condition != $value) {
        $$condition = $value;
        return $content;
      }
      break;
  }
  return '';
}



add_action('init', 'delete_duplicate_events');

function delete_duplicate_events() {
  if ( ! wp_next_scheduled( 'delete_duplicate_events' ) ) {
    wp_schedule_event( time(), 'daily', 'delete_duplicate_events' );
  }
}

add_action('delete_duplicate_events', 'delete_all');

function delete_all() {

  //   // https://stackoverflow.com/questions/6460993/get-the-keys-for-duplicate-values-in-an-array
  //   function get_keys_for_duplicate_values($my_arr, $clean = false) {
  //     if ($clean) {
  //         return array_unique($my_arr);
  //     }

  //     $dups = $new_arr = array();
  //     foreach ($my_arr as $key => $val) {
  //       if (!isset($new_arr[$val])) {
  //         $new_arr[$val] = $key;
  //       } else {
  //         if (isset($dups[$val])) {
  //           $dups[$val][] = $key;
  //         } else {
  //           $dups[$val] = array($key);
  //           // Comment out the previous line, and uncomment the following line to
  //           // include the initial key in the dups array.
  //           // $dups[$val] = array($new_arr[$val], $key);
  //         }
  //       }
  //     }
  //     return $dups;
  // }

  $allevents = get_posts(array(
    'numberposts'   => 5000,
    'post_type'     => 'tribe_events',
	  'post_author'	=> 3,
  ));

  // checks the list of all events against itself and records the IDs of all events that match post title, start date, and unmatched IDs (to prevent an event matching itself)
  // this leaves us with an array of IDs of events that have duplicates, including both post IDs.
  // the $duplicates array includes a key->value pair of eventID->startDate + eventID
  // the function above then checks $duplicates for duplicate values and returns an array of single events to be deleted. These events are then deleted by key (ID)
  $temparray =  array();
  foreach ($allevents as $event) {
    foreach ($allevents as $cEvent) {
      if ( 
        ($event->post_title == $cEvent->post_title) && 
        ( get_post_meta($event->ID, '_EventStartDate', true) == get_post_meta($cEvent->ID, '_EventStartDate', true) ) && 
        ($event->ID != $cEvent->ID) ) {
          $duplicates[$event->ID] = get_post_meta($event->ID, '_EventStartDate', true) . $event->post_title;
          //error_log('DUP: ' . $event->ID . get_post_meta($event->ID, '_EventStartDate', true) . $event->post_title);
      }
    }
  }

  $trimmed = get_keys_for_duplicate_values($duplicates);
  error_log(print_r($trimmed, true));

  foreach ($trimmed as $trim) {
      foreach ($trim as $t) {
        wp_delete_post($t, true);
        error_log('DELETED DUPLICATE EVENT ID: ' . $t);
      }
    }

    //wp_delete_post($v[0], true);
    //error_log('DELETED DUPLICATE EVENT ID: ' . $v[0]);
  
}













function  init_bg_process() {
  global  $process_all;
  $process_all    = new AE_Example_Process();
}
add_action( 'init', 'init_bg_process' );


add_action('init', 'bg_process');

function bg_process() {
  if ( ! wp_next_scheduled( 'bg_process' ) ) {
    wp_schedule_event( time(), 'daily', 'bg_process' );
  }
}

add_action('bg_process', 'bg_process_function');

function bg_process_function() {
$allevents = get_posts(array(
  'numberposts'   => 5000,
  'post_type'     => 'tribe_events',
));

// checks the list of all events against itself and records the IDs of all events that match post title, start date, and unmatched IDs (to prevent an event matching itself)
// this leaves us with an array of IDs of events that have duplicates, including both post IDs.
// the $duplicates array includes a key->value pair of eventID->startDate + eventID
// the function above then checks $duplicates for duplicate values and returns an array of single events to be deleted. These events are then deleted by key (ID)
$temparray =  array();
foreach ($allevents as $event) {
  foreach ($allevents as $cEvent) {
    if ( 
      ($event->post_title == $cEvent->post_title) && 
      ( get_post_meta($event->ID, '_EventStartDate', true) == get_post_meta($cEvent->ID, '_EventStartDate', true) ) && 
      ($event->ID != $cEvent->ID) ) {
        $duplicates[$event->ID] = get_post_meta($event->ID, '_EventStartDate', true) . $event->post_title;
        //error_log('DUP: ' . $event->ID . get_post_meta($event->ID, '_EventStartDate', true) . $event->post_title);
    }
  }
}

$trimmed = get_keys_for_duplicate_values($duplicates);

global  $process_all;
foreach ( $trimmed as $item ) {
  // error_log("task running");
  // error_log( print_r($item,true));
  $process_all->push_to_queue( $item );
}
 $process_all->save()->dispatch();
}


 
function get_keys_for_duplicate_values($my_arr, $clean = false) {
  if ($clean) {
      return array_unique($my_arr);
  }

  $dups = $new_arr = array();
  foreach ($my_arr as $key => $val) {
    if (!isset($new_arr[$val])) {
      $new_arr[$val] = $key;
    } else {
      if (isset($dups[$val])) {
        $dups[$val][] = $key;
      } else {
        $dups[$val] = array($key);
        // Comment out the previous line, and uncomment the following line to
        // include the initial key in the dups array.
        // $dups[$val] = array($new_arr[$val], $key);
      }
    }
  }
  return $dups;
}


function wpbp_http_request_args( $r, $url ) {
	$r['headers']['Authorization'] = 'Basic ' . base64_encode( 'flywheel' . ':' . 'lessismore' );

	return $r;
}
add_filter( 'http_request_args', 'wpbp_http_request_args', 10, 2);