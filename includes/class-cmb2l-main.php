<?php

/**
 * This class handles the network administration page where the questions are defined.
 */
class CMB2L_Main {

	/**
	 * @filter init
	 */
	public static function init() {
		add_filter( 'cmb2_before_form', array( __CLASS__, 'prepare_logic' ), 10, 4 );
		add_action( 'enqueue_scripts', array( __CLASS__, 'register_scripts_and_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_scripts_and_styles' ) );
	}

	/**
	 * Register the JS script and CSS style that are necessary for this page.
	 * @filter admin_enqueue_scripts
	 */
	public static function register_scripts_and_styles() {
		wp_register_script( 'cmb2-logic', CMB2_Logic::$directory_url . 'includes/js/cmb2-logic.js', array( 'jquery' ) );
		wp_register_style( 'cmb2-logic', CMB2_Logic::$directory_url . 'includes/css/cmb2-logic.css' );
	}

	public static function prepare_logic( $cmb_id, $object_id, $object_type, $cmb ) {
		$fields = $cmb->meta_box['fields'];
		$logic_data = array();

		foreach ( $fields as $slug => $field ) {
			if ( ! empty( $field['logic'] ) ) {
				$logic_data[ $slug ] = $field['logic'];
			}
		}

		wp_localize_script( 'cmb2-logic', 'cmb2_logic_data', $logic_data );
		wp_enqueue_script( 'cmb2-logic' );
		wp_enqueue_style( 'cmb2-logic' );
	}

}

add_action( 'init', array( 'CMB2L_Main', 'init' ) );

/*
This plugin will recognize logic syntax in the following pattern.

If an array has an 'operator' option then it is expected to be a group.

array(
	'op'    => 'and'|'or',
	'not'   => true|false,
	'group' => array(
		array(
			'not'   => true|false,
			'field' => <slug of relevant field>,
			'value' => <expected value of field|array of expected values>,
		),
		array(
			'op'    => 'and'|'or',
			'not'   => true|false,
			'group' => array(
				array(
					'not'   => true|false,
					'field' => <slug of relevant field>,
					'value' => <expected value of field|array of expected values>,
				),
				array(
					'not'   => true|false,
					'field' => <slug of relevant field>,
					'value' => <expected value of field|array of expected values>,
				),
			),
		)
	);
);

*/