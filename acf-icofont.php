<?php

/*
Plugin Name: Advanced Custom Fields: IcoFont
Plugin URI: https://wordpress.org/plugins/advanced-custom-fields-icofont/
Description: Adds a new 'IcoFont Icon' field to the popular Advanced Custom Fields plugin.
Version: 1.0.0
Author: ekawalec
Author URI: http://s4studio.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists('acf_plugin_icofont') ) :

	require 'assets/inc/class-ACFICFNTL.php';

	class acf_plugin_icofont {

		public function __construct()
		{
			$this->settings = array(
				'version'	=> '1.0.0',
				'url'		=> plugin_dir_url( __FILE__ ),
				'path'		=> plugin_dir_path( __FILE__ )
			);

			load_plugin_textdomain( 'acf_icofont', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

			add_action('acf/include_field_types', 	array($this, 'include_field_types'), 10 ); // v5
			add_action('acf/register_fields', 		array($this, 'include_field_types'), 10 ); // v4		
		}

		public function include_field_types( $version = false )
		{
			if ( ! $version ) {
				$version = 4;
			}

			include_once('fields/acf-icofont-v' . $version . '.php');
			
		}
		
	}

	new acf_plugin_icofont();

endif;
