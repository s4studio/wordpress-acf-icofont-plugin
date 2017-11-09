<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_field_icofont' ) ) :

	class acf_field_icofont extends acf_field
	{
		private $icons = false;	

		public function __construct( $settings )
		{				
			$this->name = 'icofont';
			$this->label = __( 'IconFont Icon', 'acf_icofont');
			$this->category = 'content';
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
		
		public function render_field_settings( $field )
		{
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Icon Preview', 'acf_icofont' ),
				'instructions'	=> '',
				'type'			=> 'message',
				'name'			=> 'icfnt_live_preview',
				'class'			=> 'live-preview'
			));

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Default Icon', 'acf_icofont' ),
				'instructions'	=> '',
				'type'			=> 'select',
				'name'			=> 'default_value',
				'class'	  		=> 'select2-icofont icofont-create',
				'choices'		=>	$field['choices'],
				'placeholder'	=> 'Choose a default icon (optional)',
				'ui'			=> 1,
				'allow_null'	=> 1,
				'ajax'			=> 1,
				'ajax_action'	=> 'acf/fields/icofont/query'
			));

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Return Value', 'acf_icofont' ),
				'instructions'	=> __( 'Specify the returned value on front end', 'acf_icofont' ),
				'type'			=> 'radio',
				'name'			=> 'save_format',
				'choices'	=>	array(
					'element'	=>	__( 'Icon Element', 'acf_icofont' ),
					'class'		=>	__( 'Icon Class', 'acf_icofont' ),
					'unicode'	=>	__( 'Icon Unicode', 'acf_icofont' ),
					'object'	=>	__( 'Icon Object', 'acf_icofont' ),
				)
			));

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Allow Null?', 'acf_icofont' ),
				'instructions'	=> '',
				'type'			=> 'radio',
				'name'			=> 'allow_null',
				'choices'	=>	array(
					1	=>	__( 'Yes', 'acf_icofont' ),
					0	=>	__( 'No', 'acf_icofont' )
				)
			));

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Show Icon Preview', 'acf_icofont' ),
				'instructions'	=> __( 'Set to \'Yes\' to include a larger icon preview on any admin pages using this field.', 'acf_icofont' ),
				'type'			=> 'radio',
				'name'			=> 'show_preview',
				'choices'	=>	array(
					1	=>	__( 'Yes', 'acf_icofont' ),
					0	=>	__( 'No', 'acf_icofont' )
				)
			));

			if ( ! apply_filters( 'ACFICFNT_always_enqueue_icfnt', false ) ) {
				acf_render_field_setting( $field, array(
					'label'			=> __( 'Enqueue IconFont?', 'acf_icofont' ),
					'instructions'	=> __( 'Set to \'Yes\' to enqueue IconFOntin the footer on any pages using this field.', 'acf_icofont' ),
					'type'			=> 'radio',
					'name'			=> 'enqueue_icofont',
					'choices'	=>	array(
						1	=>	__( 'Yes', 'acf_icofont' ),
						0	=>	__( 'No', 'acf_icofont' )
					)
				));
			}
		}

		public function render_field( $field )
		{	
			if ( $field['allow_null'] ) {
				$select_value = $field['value'];
			} else {
				$select_value = ( 'null' != $field['value'] ) ? $field['value'] : $field['default_value'];
			}
			?>
			<input type="hidden" id="<?php echo $field['id']; ?>-input" name="<?php echo $field['name']; ?>" value="<?php echo $select_value; ?>">

			<?php if ( $field['show_preview'] ) : ?>
				<div class="icon_preview"></div>
			<?php endif; ?>

			<select id="<?php echo $field['id']; ?>" class="select2-icofont icofont-edit" name="<?php echo esc_attr($field['name']) ?>" data-ui="1" data-ajax="1" data-multiple="0" data-placeholder="- Select -" data-allow_null="<?php echo $field['allow_null']; ?>">
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
			$url = $this->settings['url'];
			$version = $this->settings['version'];
			
			wp_register_script( 'acf-input-icofont', "{$url}assets/js/input-v5.js", array('acf-input'), $version );
			wp_enqueue_script('acf-input-icofont');

			wp_register_style( 'acf-input-icofont', "{$url}assets/css/input.css", array('acf-input'), $version );
			wp_enqueue_style('acf-input-icofont');

			if ( apply_filters( 'ACFICFNT_admin_enqueue_icfnt', true ) ) {
				wp_register_style( 'acf-input-icofont_library', $this->get_icfnt_url(), array('acf-input') );
				wp_enqueue_style('acf-input-icofont_library');
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
	
		public function format_value( $value, $post_id, $field )
		{
			if ( 'null' == $value ) {
				return false;
			}

			if ( empty( $value ) ) {
				return $value;
			}

			if ( isset( $this->icons['details'][ $value ] ) ) {
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
			}

			return $value;
		}
	}

	new acf_field_icofont( $this->settings );

endif;
