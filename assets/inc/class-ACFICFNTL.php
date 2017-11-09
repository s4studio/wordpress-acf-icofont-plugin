<?php
/**
 * =======================================
 * Advanced Custom Fields IconFont Loader
 * =======================================
 * 
 * 
 * @author ekawalec
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACFICFNTL
{

	public $api_endpoint		= '';//'https://data.jsdelivr.com/v1/package/gh/FortAwesome/icofont';
	public $cdn_baseurl			= '';//'https://cdn.jsdelivr.net/icofont/';
	public $cdn_filepath		= '/css/icofont.css';
	public $override_version	= false;
	public $current_version		= false;

	public function init()
	{
		$this->api_endpoint		= apply_filters( 'ACFICFNT_api_endpoint', $this->api_endpoint );
		$this->cdn_baseurl		= apply_filters( 'ACFICFNT_cdn_baseurl',  plugins_url().'/'.plugin_basename(dirname(dirname( __FILE__ ))) );
		$this->cdn_filepath		= apply_filters( 'ACFICFNT_cdn_filepath', $this->cdn_filepath );
		$this->override_version	= apply_filters( 'ACFICFNT_override_version', false );

		$this->current_version	= get_option( 'ACFICFNT_current_version' );

		/*if ( $this->override_version ) {
			$this->current_version = $this->override_version;
		} else if ( ! $this->current_version ) {
			$this->current_version = $this->check_latest_version();
		}*/

		/*if ( ! $this->override_version && ! wp_next_scheduled ( 'ACFICFNT_refresh_latest_icons' ) ) {
			wp_schedule_event( time(), 'daily', 'ACFICFNT_refresh_latest_icons' );
		}*/

		add_action( 'ACFICFNT_refresh_latest_icons', array( $this, 'refresh_latest_icons' ) );
		add_action( 'wp_ajax_acf/fields/icofont/query', array( $this, 'select2_ajax_request' ) );
		add_filter( 'ACFICFNT_get_icons', array( $this, 'get_icons' ), 5, 1 );
		add_filter( 'ACFICFNT_get_icfnt_url', array( $this, 'get_icfnt_url' ), 5, 1 );
	}

	public function select2_ajax_request()
	{
		if ( ! acf_verify_ajax() ) {
			die();
		}

		$response = $this->get_ajax_query( $_POST );

		acf_send_ajax_results( $response );
	}

	private function get_ajax_query( $options = array() )
	{
   		$options = acf_parse_args($options, array(
			'post_id'		=> 0,
			's'				=> '',
			'field_key'		=> '',
			'paged'			=> 1
		));

   		$results = array();
   		$s = null;

		if ( $options['s'] !== '' ) {
			$s = strval( $options['s'] );
			$s = wp_unslash( $s );
		}

		$icfnt_icons = $this->get_icons();

		if ( $icfnt_icons ) {
			foreach( $icfnt_icons['list'] as $k => $v ) {

				$v = strval( $v );

				if ( is_string( $s ) && false === stripos( $v, $s ) ) {
					continue;
				}

				$results[] = array(
					'id'	=> $k,
					'text'	=> $v
				);
			}
		}

		$response = array(
			'results'	=> $results
		);

		return $response;
	}

	public function refresh_latest_icons()
	{
		if ( $this->override_version ) {
			return;
		}

		$latest_version = $this->check_latest_version( false );

		if ( ! $this->current_version || ! $latest_version ) {
			return;
		}

		if ( version_compare( $this->current_version, $latest_version, '<' ) ) {
			update_option( 'ACFICFNT_current_version', $latest_version, false );
			$this->current_version = $latest_version;

			$this->get_icons();
		}
	}

	private function check_latest_version( $update_option = true )
	{
		//$latest_version = 'latest';
		$latest_version = '';

		/*$remote_get = wp_remote_get( $this->api_endpoint );

		if ( ! is_wp_error( $remote_get ) ) {
			$response_json = wp_remote_retrieve_body( $remote_get );

			if ( $response_json ) {
				$response = json_decode( $response_json );

				if ( isset( $response->versions ) && ! empty( $response->versions ) ) {
					$latest_version = max( $response->versions );
					$latest_version = ltrim( $latest_version, 'v' );

					if ( $update_option ) {
						update_option( 'ACFICFNT_current_version', $latest_version, false );
					}
				}
			}
		}*/

		return $latest_version;
	}

	public function get_icons()
	{
		$icfnt_icons = get_option( 'ACFICFNT_icon_data' );

		if ( empty( $icfnt_icons ) ) {

			//$request_url	= $this->cdn_baseurl . $this->current_version . $this->cdn_filepath;
			$request_url	=   $this->cdn_baseurl . $this->cdn_filepath;
			$remote_get		= wp_remote_get( $request_url );
			if ( ! is_wp_error( $remote_get ) ) {
				$response = wp_remote_retrieve_body( $remote_get );

				if ( ! empty( $response ) ) {
					$icons = $this->find_icons( $response );

					if ( ! empty( $icons['details'] ) ) {
						$icfnt_icons = array(
							$this->current_version => $icons
						);

						update_option( 'ACFICFNT_icon_data', $icfnt_icons, false );
					}
				}
			}
		}

		if ( isset( $icfnt_icons[ $this->current_version ] ) ) {
			return $icfnt_icons[ $this->current_version ];
		} else {
			return false;
		}
	}

	public function get_icfnt_url()
	{
		return $this->cdn_baseurl . $this->cdn_filepath;
	}

	private function find_icons( $css )
	{
		// Modified from Better IconFont Library - Thanks Mickey Kay
		preg_match_all( '/\.((?:icon-|icofont-)[^,}]*)\s*:before\s*{\s*(?:content:)\s*"(\\\\[^"]+)"/s', $css, $matches );

		$classes	= array_reverse( $matches[1] );
		$hex_codes	= array_reverse( $matches[2] );

		$icons = array(
			'list'		=> array(),
			'details'	=> array(),
			'prefix'	=> false
		);

		foreach ( $classes as $index => $class ) {
			if ( ! empty( $hex_codes[ $index ] ) ) {
				$hex = $hex_codes[ $index ];
			}

			if ( ! $icons['prefix'] ) {
				$icons['prefix'] = ( 0 === strpos( $class, 'icon-' ) ) ? 'icon-' : 'icofont-';
			}

			$class_nicename	= str_replace( array( 'icofont-', 'icon-' ), '', $class );
			$unicode		= '&#x' . ltrim( $hex, '\\') . ';';

			if ( 'icofont-' == $icons['prefix'] ) {
				$element = '<i class="icofont ' . $class . '" aria-hidden="true"></i>';
			} else {
				$element = '<i class="' . $class . '" aria-hidden="true"></i>';
			}

			$icons['list'][ $class ] = $unicode . ' ' . $class_nicename;

			$icons['details'][ $class ] = array(
				'element'	=> $element,
				'class'		=> $class,
				'hex'		=> $hex,
				'unicode'	=> $unicode
			);
		}

		ksort( $icons['list'] );

		$icons['list'] = array( null => '' ) + $icons['list'];

		return $icons;
	}
}

add_action(	'acf/include_field_types', array( new ACFICFNTL, 'init' ), 5 ); // v5
add_action(	'acf/register_fields', array( new ACFICFNTL, 'init' ), 5 ); // v4
