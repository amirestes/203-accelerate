<?php
/*
Plugin Name: Ninja Forms Styler lite
Plugin URI:  https://wpmonks.com
Description: Style your Ninja Forms with live preview in Customizer
Version:     3.2
Author:      Sushil Kumar
License:     GPL2License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

define( "NF_STYLER_DIR", WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ) );
define( "NF_STYLER_URL", plugins_url() . "/" . basename( dirname( __FILE__ ) ) );

include_once 'helpers/utils/responsive.php';
//Main class of Ninja Forms Styler
class NF_Styler_Customizer_Lite {
	public $all_found_forms_ids = array();
	/**
	 *  method for all hooks
	 *
	 * @since  v1.0
	 * @author Ninja Styler
	 */
	function __construct() {
		add_action( 'customize_register', array( $this, 'nf_styler_customize_register' ) ) ;
		add_action( 'customize_preview_init', array( $this, 'nf_styler_live_preview' ) );
		register_activation_hook( __FILE__, array( $this, 'nf_styler_welcome_screen_activate' ) );
		add_action( 'customize_save_after', array( $this, 'nf_styler_action_after_saving' ) );
		$this->all_found_forms_ids = '';
		add_action( 'ninja_forms_before_container', array( $this, 'ninja_forms_display_before_form' ) );
		add_action( 'init', array( $this, 'nf_styler_enable_admin_bar' ) );
		add_action ( 'customize_controls_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	function nf_styler_enable_admin_bar() {
		$nf_styler_genreal_options = get_option( 'nf_styler_general_settings' ) ;
		$is_admin_bar_enabled = isset( $nf_styler_genreal_options['admin-bar'] )?$nf_styler_genreal_options['admin-bar']:true;
		if ( current_user_can( 'manage_options' ) && $is_admin_bar_enabled ) {
			add_filter( 'show_admin_bar', '__return_true', 999 );
		}
	}

	function admin_scripts() {
		wp_enqueue_style( 'nouislider', NF_STYLER_URL . '/css/nf_styler.css' );
		wp_enqueue_style( 'nf_styler_admin', NF_STYLER_URL . '/css/nouislider.css' );
		wp_enqueue_style( 'nf_styler_customizer_styles', NF_STYLER_URL . '/css/customizer-controls.css' );
		wp_enqueue_script( 'nf_styler_customizer_controls', NF_STYLER_URL . '/js/customizer-controls/customizer-controls.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'nouislider', NF_STYLER_URL . '/js/nouislider.min.js', array( 'jquery','customize-controls' ), '', true );
		wp_enqueue_script( 'nf_styler_auto_save_form', NF_STYLER_URL . '/js/customize.js', array( 'jquery','customize-controls' ), '', true );
	}

	/**
	 *  shows live preview of css changes
	 *
	 * @since  v1.0
	 * @author Ninja Styler
	 * @return null
	 */
	function nf_styler_live_preview() {
		$current_form_id = get_option( 'nf_styler_select_form_id' );
		wp_enqueue_script( 'nf_styler_show_live_changes', NF_STYLER_URL . '/js/live-preview-changes.js', array( 'jquery', 'customize-preview' ), '', true );
		wp_localize_script( 'nf_styler_show_live_changes', 'nf_styler_localize_current_form', $current_form_id );

	}

	/**
	 *  Function that adds panels, sections, settings and controls
	 *
	 * @since  v1.0
	 * @author Ninja Styler
	 * @param main       wp customizer object
	 * @return null
	 */

	function nf_styler_customize_register( $wp_customize ) {
		include_once 'helpers/fonts.php';
		$current_form_id = get_option( 'nf_styler_select_form_id' );
		$border_types = array( "none" => "None", "solid" =>"Solid", "dotted"=> "Dotted", "dashed"=> "Dashed", "double"=> "Double", "groove"=> "Groove", "ridge"=> "Ridge", "inset"=> "Inset", "outset"=> "Outset" );
		$align_pos = array( "left" =>"Left", "center" => "Center", "right" => "Right", "justify" => "Justify" );
		$font_style_choices = array(
			'bold'  => 'Bold',
			'italic' => 'Italic',
			'uppercase' => 'Uppercase',
			'underline' => 'underline'
		);

		$wp_customize->add_panel( 'nf_styler_panel', array(
				'title' => __( 'Ninja Forms Styler' ),
				'description' => '<p> Craft your Forms</p>', // Include html tags such as <p>.
				'priority' => 160, // Mixed with top-level-section hierarchy.
			) );

		//hidden field to get form id in jquery
		//var_dump($_GET);

		if ( ! array_key_exists( 'autofocus', $_GET ) ) {

			$wp_customize->add_setting( 'nf_styler_hidden_field_for_form_id' , array(
					'default'     => $current_form_id,
					'transport'   => 'postMessage',
					'type' => 'option'
				) );

			$wp_customize->add_control( 'nf_styler_hidden_field_for_form_id', array(
					'type' => 'hidden',
					'priority' => 10, // Within the section.
					'section' => 'nf_styler_select_form_section', // Required, core or custom.
					'input_attrs' => array(
						'value' => $current_form_id,
						'id' => 'nf_styler_hidden_field_for_form_id'
					),
				) );
		}
		include 'helpers/customizer-controls/margin-padding.php';
		include 'helpers/customizer-controls/desktop-text-input.php';
		include 'helpers/customizer-controls/tab-text-input.php';
		include 'helpers/customizer-controls/mobile-text-input.php';
		include 'helpers/customizer-controls/custom-controls.php';
		include 'helpers/customizer-controls/text-alignment.php';
		include 'helpers/customizer-controls/font-style.php';
		include 'includes/class-nf-customizer-range-slider.php';
		include 'includes/form-select.php';
		include 'includes/general-settings.php';
		do_action( 'nf_styler_add_theme_section', $wp_customize, $current_form_id );
		include 'includes/form-wrapper.php';
		include 'includes/form-title.php';
		include 'includes/field-labels.php';
		include 'includes/field-descriptions.php';
		include 'includes/placeholders.php';

		include 'includes/text-fields.php';
		include 'includes/dropdown-fields.php';
		include 'includes/radio-inputs.php';
		include 'includes/checkbox-inputs.php';
		include 'includes/paragraph-textarea.php';
		include 'includes/confirmation-message.php';
		include 'includes/error-message.php';
		include 'includes/submit-button.php';
	
	} // main customizer function ends here

	function get_form_styles( $form_id, $category ) {


		$settings = get_option( 'nf_styler_form_id_' . $form_id );

		if ( empty( $settings ) ) {
			return;
		}

		$input_styles = '';
		$input_styles .= empty( $settings[$category]['color'] )?'':'color:' . $settings[$category]['color'] . ';';
		$input_styles .= empty( $settings[$category]['background-color'] )?'':'background-color:' . $settings[$category]['background-color'] . ';';
		$input_styles .= empty( $settings[$category]['background-color1'] )?'':'background:-webkit-linear-gradient(to left,' . $settings[$category]['background-color'] . ',' . $settings[$category]['background-color1'] . ');';
		$input_styles .= empty( $settings[$category]['background-color1'] )?'':'background:linear-gradient(to left,' . $settings[$category]['background-color'] . ',' . $settings[$category]['background-color1'] . ');';

		//$input_styles.= empty( $settings[$category]['padding'] )?'':'padding:'. $settings[$category]['padding'].';';
		$input_styles .= empty( $settings[$category]['width'] )?'':'width:' . $settings[$category]['width'] . $this->nf_styler_add_px_to_value( $settings[$category]['width'] ) . ';';
		$input_styles .= empty( $settings[$category]['height'] )?'':'height:' . $settings[$category]['height'] . $this->nf_styler_add_px_to_value( $settings[$category]['height'] ) . ';';
		$input_styles .= empty( $settings[$category]['line-height'] )?'':'line-height:' . $settings[$category]['line-height'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['line-height'] ) . ';';
		$input_styles .= empty( $settings[$category]['title-position'] )?'':'text-align:' . $settings[$category]['title-position'] . ';';
		$input_styles .= empty( $settings[$category]['text-align'] )?'':'text-align:' . $settings[$category]['text-align'] . ';';
		$input_styles .= empty( $settings[$category]['error-position'] )?'':'text-align:' . $settings[$category]['error-position'] . ';';
		$input_styles .= empty( $settings[$category]['description-position'] )?'':'text-align:' . $settings[$category]['description-position'] . ';';

		$input_styles .= empty( $settings[$category]['title-color'] )?'':'color:' . $settings[$category]['title-color'] . ';';
		$input_styles .= empty( $settings[$category]['font-color'] )?'':'color:' . $settings[$category]['font-color'] . ';';
		
		$input_styles .= empty( $settings[$category]['button-color'] )?'':'background-color:' . $settings[$category]['button-color'] . ';';
		$input_styles .= empty( $settings[$category]['description-color'] )?'':'color:' . $settings[$category]['description-color'] . ';';

		$input_styles .= empty( $settings[$category]['font-family'] )?'':'font-family:' . $settings[$category]['font-family'] . ';';
		$input_styles .= empty( $settings[$category]['font-size'] )?'':'font-size:' . $settings[$category]['font-size'] . $this->nf_styler_add_px_to_value( $settings[$category]['font-size'] ) . ';';
		$input_styles .= empty( $settings[$category]['max-width'] )?'':'max-width:' . $settings[$category]['max-width'] . $this->nf_styler_add_px_to_value( $settings[$category]['max-width'] ) . ';';
		$input_styles .= empty( $settings[$category]['maximum-width'] )?'':'max-width:' . $settings[$category]['maximum-width'] . $this->nf_styler_add_px_to_value( $settings[$category]['maximum-width'] ) . ';';
		$input_styles .= !isset( $settings[$category]['margin'] )?'':'margin:' . $settings[$category]['margin'] . ';';
		$input_styles .= !isset( $settings[$category]['padding'] )?'':'padding:' . $settings[$category]['padding'] . ';';

		$input_styles .= !isset( $settings[$category]['border-size'] )?'':'border-width:' . $settings[$category]['border-size'] . $this->nf_styler_add_px_to_value( $settings[$category]['border-size'] ) . ';';
		$input_styles .= empty( $settings[$category]['border-color'] )?'':'border-color:' . $settings[$category]['border-color'] . ';';
		$input_styles .= empty( $settings[$category]['border-type'] )?'':'border-style:' . $settings[$category]['border-type'] . ';';
		$input_styles .= empty( $settings[$category]['border-bottom'] )?'':'border-bottom-style:' . $settings[$category]['border-bottom'] . ';';
		$input_styles .= !isset( $settings[$category]['border-bottom-size'] )?'':'border-bottom-width:' . $settings[$category]['border-bottom-size'] . $this->nf_styler_add_px_to_value( $settings[$category]['border-bottom-size'] ) . ';';
		$input_styles .= empty( $settings[$category]['border-bottom-color'] )?'':'border-bottom-color:' . $settings[$category]['border-bottom-color'] . ';';



		$input_styles .= empty( $settings[$category]['background-image-url'] )?'':'background: url(' . $settings[$category]['background-image-url'] . ') no-repeat;';
		$input_styles .= empty( $settings[$category]['border-bottom-color'] )?'':'border-bottom-color:' . $settings[$category]['border-bottom-color'] . ';';
		if ( isset( $settings[$category]['display'] ) ) {
			$input_styles .=  $settings[$category]['display'] ?'display:none;':'display:inherit;';
		}
		if ( isset( $settings[$category]['border-radius'] ) ) {
			$input_styles .= 'border-radius:' . $settings[$category]['border-radius'] . $this->nf_styler_add_px_to_value( $settings[$category]['border-radius'] ) . ';';
			$input_styles .= '-web-border-radius:' . $settings[$category]['border-radius'] . $this->nf_styler_add_px_to_value( $settings[$category]['border-radius'] ) . ';';
			$input_styles .= '-moz-border-radius:' . $settings[$category]['border-radius'] . $this->nf_styler_add_px_to_value( $settings[$category]['border-radius'] ) . ';';
		}
		$input_styles .= empty( $settings[$category]['custom-css'] )?'':$settings[$category]['custom-css'] . ';';

		$input_styles .= !isset( $settings[ $category ]['padding-left'] ) ? '' : 'padding-left:' . $settings[ $category ]['padding-left'] . $this->nf_styler_add_px_to_value( $settings[$category]['padding-left'] )  . ';';
		$input_styles .= !isset( $settings[ $category ]['padding-right'] ) ? '' : 'padding-right:' . $settings[ $category ]['padding-right'] . $this->nf_styler_add_px_to_value( $settings[$category]['padding-right'] )  . ';';
		$input_styles .= !isset( $settings[ $category ]['padding-top'] ) ? '' : 'padding-top:' . $settings[ $category ]['padding-top'] . $this->nf_styler_add_px_to_value( $settings[$category]['padding-top'] )  . ';';
		$input_styles .= !isset( $settings[ $category ]['padding-bottom'] ) ? '' : 'padding-bottom:' . $settings[ $category ]['padding-bottom'] . $this->nf_styler_add_px_to_value( $settings[$category]['padding-bottom'] )  . ';';

		$input_styles .= !isset( $settings[ $category ]['margin-left'] ) ? '' : 'margin-left:' . $settings[ $category ]['margin-left'] . $this->nf_styler_add_px_to_value( $settings[$category]['margin-left'] ) . ';';
		$input_styles .= !isset( $settings[ $category ]['margin-right'] ) ? '' : 'margin-right:' . $settings[ $category ]['margin-right'] . $this->nf_styler_add_px_to_value( $settings[$category]['margin-right'] ) . ';';
		$input_styles .= !isset( $settings[ $category ]['margin-top'] ) ? '' : 'margin-top:' . $settings[ $category ]['margin-top'] . $this->nf_styler_add_px_to_value( $settings[$category]['margin-top'] ) . ';';
		$input_styles .= !isset( $settings[ $category ]['margin-bottom'] ) ? '' : 'margin-bottom:' . $settings[ $category ]['margin-bottom'] . $this->nf_styler_add_px_to_value( $settings[$category]['margin-bottom'] ) . ';';

		if ( ! empty( $settings[ $category ]['font-style'] ) ) {
			$font_styles = explode( '|', $settings[ $category ]['font-style'] );

				foreach ( $font_styles as $value ) {
					switch ( $value ) {
					case 'bold':
						$input_styles .= 'font-weight: bold;';
						break;
					case 'italic':
						$input_styles .= 'font-style: italic;';
						break;
					case 'uppercase':
						$input_styles .= 'text-transform: uppercase;';
						break;
					case 'underline':
						$input_styles .= 'text-decoration: underline;';
						break;
					default:
						break;
					}
			}
		}
		
		return $input_styles;
	}

	public function nf_styler_get_saved_styles_tab( $form_id, $category, $important = '' ) {
		$settings = get_option( 'nf_styler_form_id_' . $form_id );
		if ( empty( $settings ) ) {
			return;
		}
		$input_styles  = '';
		$input_styles .= empty( $settings[ $category ]['width-tab'] ) ? '' : 'width:' . $settings[ $category ]['width-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['max-width-tab'] ) ? '' : 'width:' . $settings[ $category ]['max-width-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['max-width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['maximum-width-tab'] ) ? '' : 'width:' . $settings[ $category ]['maximum-width-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['maximum-width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['height-tab'] ) ? '' : 'height:' . $settings[ $category ]['height-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['height-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-size-tab'] ) ? '' : 'font-size:' . $settings[ $category ]['font-size-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['font-size-tab'] ) . ' ' . $important . ';';

		$input_styles .= empty( $settings[ $category ]['line-height-tab'] ) ? '' : 'line-height:' . $settings[ $category ]['line-height-tab'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['line-height-tab'] ) . ' ' . $important . ';';
		return $input_styles;
	}

	public function nf_styler_get_saved_styles_phone( $form_id, $category, $important = '' ) {
		$settings = get_option( 'nf_styler_form_id_' . $form_id );
		if ( empty( $settings ) ) {
			return;
		}
		$input_styles  = '';
		$input_styles .= empty( $settings[ $category ]['width-phone'] ) ? '' : 'width:' . $settings[ $category ]['width-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['max-width-phone'] ) ? '' : 'width:' . $settings[ $category ]['max-width-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['max-width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['maximum-width-phone'] ) ? '' : 'width:' . $settings[ $category ]['maximum-width-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['maximum-width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['height-phone'] ) ? '' : 'height:' . $settings[ $category ]['height-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['height-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-size-phone'] ) ? '' : 'font-size:' . $settings[ $category ]['font-size-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['font-size-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['line-height-phone'] ) ? '' : 'line-height:' . $settings[ $category ]['line-height-phone'] . $this->nf_styler_add_px_to_value( $settings[ $category ]['line-height-tab'] ) . ' ' . $important . ';';

		return $input_styles;
	}
	/**
	 * Function to add px if not available
	 */

	function nf_styler_add_px_to_value( $value ) {
		$int_parsed = (int) $value;
		if ( ctype_digit( $value ) ) {
			$value = 'px';
		}

		else {
			$value = '';
		}
		return $value;
	}

	function nf_styler_welcome_screen_activate() {
		set_transient( 'nf_styler_welcome_activation_redirect', true, 30 );
	}


	
	function nf_styler_action_after_saving() {

		//get name of style to be deleted

		$style_to_be_deleted = get_option( 'nf_styler_general_settings' );
		if ( $style_to_be_deleted['reset-styles'] != -1 || ! empty( $style_to_be_deleted['reset-styles'] ) ) {
			delete_option( 'nf_styler_form_id_' . $style_to_be_deleted['reset-styles'] );
			$style_to_be_deleted['reset-styles'] = -1;
			update_option( 'nf_styler_general_settings', $style_to_be_deleted );

		}

	}

	function ninja_forms_display_before_form( $form_id ) {
		//show css in frontend
		$style_current_form = get_option( 'nf_styler_form_id_' . $form_id );
		if ( ! empty( $style_current_form ) ) {
			$css_form_id = $form_id;
			$main_class_object = $this;
			include 'display/class-styles.php';
		}
		do_action( 'nf_styler_after_post_style_display', $this );

	}
}// class ends here

add_action( 'plugins_loaded', 'nf_styler_customizer_lite' );

function nf_styler_customizer_lite() {
	new NF_Styler_Customizer_Lite();
}
