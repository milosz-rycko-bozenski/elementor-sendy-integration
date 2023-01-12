<?php
/* Plugin Name: SIMPLY Elementor Forms integration with Sendy.co via server API */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function simply_add_new_sendy_form_action( $form_actions_registrar ) {
	include_once( __DIR__ . '/form-actions/sendy-action.php' );
	$form_actions_registrar->register( new \Simply_Sendy_Action_After_Submit() );
}

add_action( 'elementor_pro/forms/actions/register', 'simply_add_new_sendy_form_action' );