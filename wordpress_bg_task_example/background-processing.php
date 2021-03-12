<?php

if ( ! class_exists( 'AE_WP_Background_Process' ) ) {
include ( get_stylesheet_directory().'/includes/wp-background-process.php');
}
if ( ! class_exists( 'WP_Async_Request' ) ) {
	include ( get_stylesheet_directory().'/includes/wp-async-request.php');
}


// require_once get_stylesheet_directory() . '/includes/wp-async-request.php';

class AE_Example_Process extends AE_WP_Background_Process {

	//use WP_Example_Logger;

	/**
	 * @var string
	 */
	protected $action = 'ae_remove_dup';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		//$message = $this->get_message( $item );

		// $this->delete_all();
    
         wp_delete_post($item[0], true);
        error_log('DELETED DUPLICATE EVENT ID: ' . $item[0]);
		//$this->log( $message );

		return false;
    }

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}