<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// check if class already exists
if ( ! class_exists( 'acf_field_icofont' ) ) :

	class acf_field_icofont extends acf_field {

		private $icons = false;

		// vars
		var $settings, // will hold info such as dir / path
			$defaults; // will hold default field options

		public function __construct( $settings )
		{
			$this->name = 'icofont';
			$this->label = __( 'IconFont Icon', 'acf_icofont' );
			$this->category = 'Content';
			$this->settings = $settings;

			$this->defaults = array(
				'enqueue_icofont' 		=>	0,
				'allow_null' 		=>	0,
				'show_preview'		=>	1,
				'save_format'		=>  'element',
				'default_value'		=>	'',
				'icfnt_live_preview'	=>	'',
				'choices'			=>	$this->get_icons('list')
			);

	    	parent::__construct();


			if ( apply_filters( 'ACFICFNT_always_enqueue_icfnt', false ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
			} else {
				add_filter('acf/load_field', array( $this, 'maybe_enqueue_font_awesome' ) );
			}
		}

		private function get_icons( $format = 'list' )
		{
			if ( ! $this->icons ) {
				$this->icons = apply_filters( 'ACFICFNT_get_icons', array() );
			}

			return $this->icons[ $format ];
		}

		private function get_icfnt_url()
		{
			return apply_filters( 'ACFICFNT_get_icfnt_url', '' );
		}

		public function create_options( $field )
		{
			$field = array_merge( $this->defaults, $field );
			$key = $field['name'];
			?>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Icon Preview', 'acf_icofont' ); ?></label>
				</td>
				<td>
					<div class="icofont-field-wrapper">
						<div class="icfnt_live_preview"></div>
					</div>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Default Icon', 'acf_icofont' ); ?></label>
				</td>
				<td>
					<div class="icofont-field-wrapper">
						<?php
							do_action('acf/create_field', array(
								'type'    =>  'select',
								'name'    =>  'fields[' . $key . '][default_value]',
								'value'   =>  $field['default_value'],
								'class'	  =>  'chosen-icofont icofont-create',
								'choices' =>  $field['choices']
							));
						?>
					</div>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Return Value', 'acf_icofont' ); ?></label>
					<p class="description"><?php _e( 'Specify the returned value on front end', 'acf_icofont' ); ?></p>
				</td>
				<td>
					<?php 
						do_action('acf/create_field', array(
							'type'	=>	'radio',
							'name'	=>	'fields['.$key.'][save_format]',
							'value'	=>	$field['save_format'],
							'choices'	=>	array(
								'element'	=>	__("Icon Element",'acf_icofont'),
								'class'		=>	__("Icon Class",'acf_icofont'),
								'unicode'	=>	__("Icon Unicode",'acf_icofont'),
								'object'	=>	__("Icon Object",'acf_icofont'),
							),
							'layout'	=>	'vertical',
						));
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Allow Null?', 'acf_icofont' ); ?></label>
				</td>
				<td>
					<?php 
						do_action('acf/create_field', array(
							'type'	=>	'radio',
							'name'	=>	'fields['.$key.'][allow_null]',
							'value'	=>	$field['allow_null'],
							'choices'	=>	array(
								1	=>	__( 'Yes', 'acf_icofont' ),
								0	=>	__( 'No', 'acf_icofont' ),
							),
							'layout'	=>	'horizontal',
						));
					?>
				</td>
			</tr>

			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Show Icon Preview', 'acf_icofont' ); ?></label>
					<p class="description"><?php _e( 'Set to \'Yes\' to include a larger icon preview on any admin pages using this field.', 'acf_icofont' ); ?></p>
				</td>
				<td>
					<?php 
						do_action('acf/create_field', array(
							'type'	=>	'radio',
							'name'	=>	'fields['.$key.'][show_preview]',
							'value'	=>	$field['show_preview'],
							'choices'	=>	array(
								1	=>	__( 'Yes', 'acf_icofont' ),
								0	=>	__( 'No', 'acf_icofont' ),
							),
							'layout'	=>	'horizontal',
						));
					?>
				</td>
			</tr>

			<?php if ( ! apply_filters( 'ACFICFNT_always_enqueue_icfnt', false ) ) : ?>
				<tr class="field_option field_option_<?php echo $this->name; ?>">
					<td class="label">
						<label><?php _e( 'Enqueue IconFont?', 'acf_icofont' ); ?></label>
						<p class="description"><?php _e( 'Set to \'Yes\' to enqueue IconFOntin the footer on any pages using this field.', 'acf_icofont' ); ?></p>
					</td>
					<td>
						<?php 
							do_action('acf/create_field', array(
								'type'	=>	'radio',
								'name'	=>	'fields['.$key.'][enqueue_icofont]',
								'value'	=>	$field['enqueue_icofont'],
								'choices'	=>	array(
									1	=>	__( 'Yes', 'acf_icofont' ),
									0	=>	__( 'No', 'acf_icofont' ),
								),
								'layout'	=>	'horizontal',
							));
						?>
					</td>
				</tr>
			<?php endif; ?>
			<?php
		}

		public function create_field( $field )
		{
			if ( $field['allow_null'] ) {
				$select_value = $field['value'];
			} else {
				$select_value = ( 'null' != $field['value'] ) ? $field['value'] : $field['default_value'];
			}
			?>
			<?php if ( $field['show_preview'] ) : ?>
				<div class="icon_preview"></div>
			<?php endif; ?>

			<select id="<?php echo $field['id']; ?>" class="chosen-icofont icofont-edit" name="<?php echo esc_attr($field['name']) ?>" data-ui="1" data-ajax="1" data-multiple="0" data-placeholder="- Select -" data-allow_null="<?php echo $field['allow_null']; ?>">
				<?php
					$icons = $this->get_icons('list');
					if ( $icons ) :
						foreach ( $icons as $value => $label ) :
							?>
							<option value="<?php echo $value; ?>" <?php selected( $select_value, $value ); ?>><?php echo $label; ?></option>
							<?php
						endforeach;
					endif;
				?>
			</select>
			<?php
		}

		public function input_admin_enqueue_scripts()
		{
			$this->enqueue_admin_scripts( array( 'acf-input' ) );
		}

		public function field_group_admin_enqueue_scripts()
		{
			$this->enqueue_admin_scripts( array( 'acf-field-group' ) );
		}

		private function enqueue_admin_scripts( $dependencies = array() )
		{
			$url = $this->settings['url'];
			$version = $this->settings['version'];

			if ( apply_filters( 'ACFICFNT_load_chosen', true ) ) {
				wp_enqueue_script( 'chosen', "{$url}assets/inc/chosen/chosen.jquery.min.js", array('jquery'), '1.7.0' );
				wp_enqueue_style( 'chosen', "{$url}assets/inc/chosen/chosen.min.css", '', '1.7.0' );
			}

			wp_register_script( 'acf-input-icofont', "{$url}assets/js/input-v4.js", $dependencies, $version );
			wp_enqueue_script( 'acf-input-icofont' );
			wp_localize_script( 'acf-input-icofont', 'ACFICFNT', array(
				'chosen'		=> apply_filters( 'ACFICFNT_load_chosen', true ),
				'nonce'			=> wp_create_nonce( 'ACFICFNT_nonce' ),
				'is_rtl'		=> is_rtl(),
				'no_results'	=> __( 'Cannot find icon', 'acf_icofont' ) . ' : '
			));

			wp_register_style( 'acf-input-icofont', "{$url}assets/css/input.css", $dependencies, $version );
			wp_enqueue_style( 'acf-input-icofont' );

			if ( apply_filters( 'ACFICFNT_admin_enqueue_icfnt', true ) ) {
				wp_register_style( 'acf-input-icofont_library', $this->get_icfnt_url(), $dependencies );
				wp_enqueue_style( 'acf-input-icofont_library' );
			}
		}

		public function maybe_enqueue_font_awesome( $field )
		{
			if ( 'icofont' == $field['type'] && $field['enqueue_icofont'] ) {
				add_action( 'wp_footer', array( $this, 'frontend_enqueue_scripts' ) );
			}

			return $field;
		}

		public function frontend_enqueue_scripts()
		{
			wp_register_style( 'icofont', $this->get_icfnt_url() );
			wp_enqueue_style('icofont');
		}
	
		public function format_value_for_api( $value, $post_id, $field )
		{
			if ( 'null' == $value ) {
				return false;
			}

			if ( empty( $value ) ) {
				return $value;
			}

			switch ( $field['save_format'] ) {
				case 'element':
				case 'class':
				case 'unicode':
					$value = $this->icons['details'][ $value ][ $field['save_format'] ];
					break;

				case 'object':
					$value = ( object ) $this->icons['details'][ $value ];
					break;
			}

			return $value;
		}
	}

	new acf_field_icofont( $this->settings );

endif;
