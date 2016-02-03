<?php
/**
 * Ninja Forms plugin integration
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * In this file we integrate Ninja Forms with our theme:
 * 	– Add the NINJA FORMS module on Live Composer toolbar
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2015 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Disable automatic updates for 'Ninja Forms - Layout Master' plugin
// Our approach is to test premium plugin updates before releasing it to theme users
update_option( 'bti_layout_master_update', 0);

// Check if Ninja Forms and Live Composer plugins are active
if ( defined('NF_PLUGIN_VERSION') && defined( 'DS_LIVE_COMPOSER_URL' ) ) {

	/**
	 * ----------------------------------------------------------------------
	 * Helper function used to get the Ninja Form ID by form Title
	 */

	if ( ! function_exists('lbmn_get_ninjaform_id_by_title') ) {
		function lbmn_get_ninjaform_id_by_title ($form_title = '') {
			$all_forms = Ninja_Forms()->forms()->get_all();

			if( is_array($all_forms) AND !empty($all_forms) ){
				foreach ( $all_forms as $form_id ) {
					$form_data = Ninja_Forms()->form( $form_id )->get_all_settings();

					if ( stripslashes( $form_data['form_title'] ) == stripslashes($form_title) ) {
						return $form_id;
					}
				}
			}
			return false;
		}
	}

	/**
	 * ----------------------------------------------------------------------
	 * Add the NINJA FORMS module on Live Composer toolbar
	 */

	add_action('dslc_hook_register_modules',
		 create_function('', 'return dslc_register_module( "LBMN_Ninja_Forms" );')
	);

	class LBMN_Ninja_Forms extends DSLC_Module {
		var $module_id;
		var $module_title;
		var $module_icon;
		var $module_category;

		function __construct() {
			$this->module_id = 'LBMN_Ninja_Forms';
			$this->module_title = __( 'Ninja Forms', 'dslc_string' );
			$this->module_icon = 'envelope';
			$this->module_category = 'Plugins';


			// Output Ninja Forms CSS to have it properly styled on the very first drop
			if ( ! wp_script_is( 'ninja-forms-display', 'registered' ) ) {	
				ninja_forms_display_css();				 
			}
		
		}

		function options() {

			$ninja_form_choices = array();

			$ninja_form_choices[] = array(
				'label' => __( '-- Select --', 'dslc_string' ),
				'value' => 'not_set',
			);

			/**
			 * ----------------------------------------------------------------------
			 * Get all the forms available
			 */
			$all_forms = Ninja_Forms()->forms()->get_all();

			if( is_array($all_forms) AND !empty($all_forms) ){

				foreach ( $all_forms as $form_id ) {

					$form_data = Ninja_Forms()->form( $form_id )->get_all_settings();

					$ninja_form_choices[] = array(
						'label' => stripslashes( $form_data['form_title'] ),
						'value' => stripslashes( $form_data['form_title'] ) //$form_id
					);
				}
			}


			$dslc_options = array(
				
				array(
					'label' => __( 'Form Name', 'dslc_string' ),
					'id' => 'ninjaform_title',
					'std' => 'not_set',
					'type' => 'select',
					'choices' => $ninja_form_choices
				),

				array(
					'label' => __( 'Show On', 'dslc_string' ),
					'id' => 'css_show_on',
					'std' => 'desktop tablet phone',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Desktop', 'dslc_string' ),
							'value' => 'desktop'
						),
						array(
							'label' => __( 'Tablet', 'dslc_string' ),
							'value' => 'tablet'
						),
						array(
							'label' => __( 'Phone', 'dslc_string' ),
							'value' => 'phone'
						),
					),
				),
			

				/**
				 * Styling Options
				 */
				
				array(
					'label' => __( ' BG Color', 'dslc_string' ),
					'id' => 'css_main_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_main_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_main_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_main_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Radius - Top', 'dslc_string' ),
					'id' => 'css_main_border_radius_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
					'id' => 'css_main_border_radius_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_margin_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Form Block: Padding Vertical', 'dslc_string' ),
					'id' => 'css_main_padding_vertical',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Form Block: Padding Horizontal', 'dslc_string' ),
					'id' => 'css_main_padding_horizontal',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
				),

/**
 * ----------------------------------------------------------------------
 * 
 */

				/**
				 * Textbox / Textarea
				 */

				array(
					'label' => __( 'BG Color', 'dslc_string' ),
					'id' => 'css_inputs_bg_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_inputs_border_color',
					'std' => '', // 'std' => '#ddd',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_inputs_color',
					'std' => '', // 'std' => '#4d4d4d',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: BG Color', 'dslc_string' ),
					'id' => 'css_inputs_focus_bg_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: Border Color', 'dslc_string' ),
					'id' => 'css_inputs_focus_border_color',
					'std' => '', // 'std' => '#eee',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: Text Color', 'dslc_string' ),
					'id' => 'css_inputs_focus_txt_color',
					'std' => '', // 'std' => '#eee',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'input[type=text]:focus,input[type=email]:focus,textarea:focus,input[type=password]:focus,input[type=number]:focus',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_inputs_border_width',
					'std' => '', // 'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_inputs_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius', 'dslc_string' ),
					'id' => 'css_inputs_border_radius',
					'std' => '', // 'std' => '4',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'border-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				// array(
				// 	'label' => __( 'Placeholder Color', 'dslc_string' ),
				// 	'id' => 'css_placeholder_color',
				// 	'std' => '', // 'std' => '#4d4d4d',
				// 	'type' => 'color',
				// 	'refresh_on_change' => false,
				// 	'affect_on_change_el' => 'input[type=text]:-moz-placeholder',
				// 	'affect_on_change_rule' => 'color',
				// 	'section' => 'styling',
				// 	'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				// ),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_inputs_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_inputs_font_weight',
					'std' => '', // 'std' => '500',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_inputs_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height Input', 'dslc_string' ),
					'id' => 'css_inputs_line_height',
					'std' => '', // 'std' => '23',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],input[type=password],input[type=number]',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Line Height Textarea', 'dslc_string' ),
					'id' => 'css_inputs_line_height',
					'std' => '', // 'std' => '23',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'textarea',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Min-Height Textarea', 'dslc_string' ),
					'id' => 'css_textarea_min_height',
					'std' => '', // 'std' => '100',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'textarea',
					'affect_on_change_rule' => 'height',
					'ext' => 'px',
					'min' => 0,
					'max' => 500,
					'section' => 'styling',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_inputs_margin_bottom',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_inputs_padding_vertical',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_inputs_padding_horizontal',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=text],input[type=email],textarea,input[type=password],input[type=number]',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Textbox / Textarea', 'dslc_string' ),
				),


				/**
				 * Selectors
				 */

				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_checkbox_labels_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label,  .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_checkbox_labels_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_checkbox_labels_font_weight',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_checkbox_labels_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_checkbox_labels_line_height',
					'std' => '', // 'std' => '23',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .list-checkbox-wrap li label, .list-radio-wrap li label, .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Vertical Shift', 'dslc_string' ),
					'id' => 'css_checkbox_input_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".checkbox-wrap label, .optin_mailchimp-wrap input+span, .field-wrap input[type='checkbox'], .field-wrap input[type='radio']",
					'affect_on_change_rule' => 'top',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px',
					'min' => -20,
					'max' => 20,
					'increment' => 1
				),
				array(
					'label' => __( 'Input Margin Right', 'dslc_string' ),
					'id' => 'css_checkbox_input_margin_right',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".checkbox-wrap label, .optin_mailchimp-wrap input+span, .field-wrap input[type='checkbox'], .field-wrap input[type='radio']",
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px',
				),
				/*
				array(
					'label' => __( 'Margin Left', 'dslc_string' ),
					'id' => 'css_checkbox_labels_margin_left',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap label, .optin_mailchimp-wrap input+span',
					'affect_on_change_rule' => 'margin-left',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px',
				),
				*/
				array(
					'label' => __( 'Label Margin Bottom', 'dslc_string' ),
					'id' => 'css_checkbox_labels_margin_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					// 'affect_on_change_el' => '.checkbox-wrap, .optin_mailchimp-wrap',
					'affect_on_change_el' => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap li',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
				),
				array(
					'label' => __( 'Label Margin Right', 'dslc_string' ),
					'id' => 'css_checkbox_margin_right',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => ".checkbox-wrap label, .optin_mailchimp-wrap input+span, .list-radio-wrap li label, .list-checkbox-wrap li label",
					'affect_on_change_rule' => 'margin-right',
					'section' => 'styling',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_checkbox_labels_padding_vertical',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap li label, .list-checkbox-wrap li label',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_checkbox_labels_padding_horizontal',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.checkbox-wrap, .optin_mailchimp-wrap, .list-radio-wrap li label, .list-checkbox-wrap li label',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Radio / Checkboxes', 'dslc_string' ),
				),

				

				/**
				 * Select
				 */

				array(
					'label' => __( 'BG Color', 'dslc_string' ),
					'id' => 'css_select_bg_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_select_border_color',
					'std' => '', // 'std' => '#ddd',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: BG Color', 'dslc_string' ),
					'id' => 'css_select_focus_bg_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'select:focus',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: Border Color', 'dslc_string' ),
					'id' => 'css_select_focus_border_color',
					'std' => '', // 'std' => '#eee',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'select:focus',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Focused: Text Color', 'dslc_string' ),
					'id' => 'css_select_focus_txt_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'select:focus',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_select_border_width',
					'std' => '', // 'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_select_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius', 'dslc_string' ),
					'id' => 'css_select_border_radius',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'border-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_select_color',
					'std' => '', // 'std' => '#4d4d4d',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_select_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_select_font_weight',
					'std' => '', // 'std' => '500',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_select_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height Input', 'dslc_string' ),
					'id' => 'css_select_line_height',
					'std' => '', // 'std' => '23',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_select_margin_bottom',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_select_padding_vertical',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_select_padding_horizontal',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'select',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'List > Dropdown', 'dslc_string' ),
				),

				/**
				 * Text
				 */

				array(
					'label' => __( ' BG Color', 'dslc_string' ),
					'id' => 'css_text_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_text_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_text_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_text_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Top', 'dslc_string' ),
					'id' => 'css_text_border_radius_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
					'id' => 'css_text_border_radius_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_text_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Link Color', 'dslc_string' ),
					'id' => 'css_link_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc a',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Link Color: Hover', 'dslc_string' ),
					'id' => 'css_link_color_hover',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => '.nf-desc a:hover',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_text_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_text_font_weight',
					'std' => '', // 'std' => '400',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_text_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_text_line_height',
					'std' => '', // 'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_text_margin_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_text_padding_vertical',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_text_padding_horizontal',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Text Element', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_text_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.nf-desc',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Text Element', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),

				/**
				 * Hr
				 */
				array(
					'label' => __( 'Color', 'dslc_string' ) ,
					'id' => 'css_hr_bg_color',
					'std' => '', // 'std' => '#ededed',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.hr-wrap hr',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'hr', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ) ,
					'id' => 'css_hr_height',
					'std' => '', // 'std' => '1',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.hr-wrap hr',
					'affect_on_change_rule' => 'margin-bottom,padding-bottom',
					'ext' => 'px',
					'min' => 1,
					'max' => 20,
					'section' => 'styling',
					'tab' => __( 'hr', 'dslc_string' ),
				),
				array(
					'label' => __( 'Margin Top', 'dslc_string' ),
					'id' => 'css_hr_margin_top',
					'std' => '', // 'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.hr-wrap',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => __( 'hr', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Borrom', 'dslc_string' ),
					'id' => 'css_hr_margin_bottom',
					'std' => '', // 'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.hr-wrap',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => __( 'hr', 'dslc_string' ),
					'ext' => 'px',
				),
				

				/**
				 * Submit Button
				 */

				array(
					'label' => __( 'BG Color', 'dslc_string' ),
					'id' => 'css_button_bg_color',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_button_border_color',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_button_color',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Hover: BG Color', 'dslc_string' ),
					'id' => 'css_button_bg_color_hover',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'input[type=submit]:hover, button:hover',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Hover: Border Color', 'dslc_string' ),
					'id' => 'css_button_border_color_hover',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit]:hover, button:hover',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Hover: Text Color', 'dslc_string' ),
					'id' => 'css_button_color_hover',
					'std' => '', // 'std' => '#fff',
					'type' => 'color',
					'refresh_on_change' => true,
					'affect_on_change_el' => 'input[type=submit]:hover, button:hover',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_button_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_button_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius', 'dslc_string' ),
					'id' => 'css_button_border_radius',
					'std' => '', // 'std' => '3',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'border-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_button_font_size',
					'std' => '', // 'std' => '16',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_button_font_weight',
					'std' => '', // 'std' => '300',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_button_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_button_line_height',
					'std' => '', // 'std' => '21',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_button_padding_vertical',
					'std' => '', // 'std' => '14',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_button_padding_horizontal',
					'std' => '', // 'std' => '18',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'input[type=submit], button',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Submit', 'dslc_string' ),
				),
				array(
					'label' => __( 'Margin Top', 'dslc_string' ),
					'id' => 'css_button_margin_top',
					'std' => '', // 'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.submit-wrap',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => __( 'Submit', 'dslc_string' ),
					'ext' => 'px',
					'min' => -50,
					'max' => 50,
					'increment' => 1
				),


/**
 * ----------------------------------------------------------------------
 * 
 */

				array(
					'label' => ' ',
					'id' => 'pseudo_element',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '',
					'affect_on_change_rule' => '',
					'section' => 'styling',
					'tab' => '<br />',
					'ext' => 'px',
				),


/**
 * ----------------------------------------------------------------------
 * 
 */			

				array(
					'label' => __( 'Padding Top', 'dslc_string' ),
					'id' => 'css_form_field_padding_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap',
					'affect_on_change_rule' => 'padding-top',
					'section' => 'styling',
					'tab' => __( 'Field > Padding', 'dslc_string' ),
					'ext' => 'px',
				),

				array(
					'label' => __( 'Padding Bottom', 'dslc_string' ),
					'id' => 'css_form_field_padding_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap',
					'affect_on_change_rule' => 'padding-bottom',
					'section' => 'styling',
					'tab' => __( 'Field > Padding', 'dslc_string' ),
					'ext' => 'px',
				),

				array(
					'label' => __( 'Padding Left', 'dslc_string' ),
					'id' => 'css_form_field_padding_left',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap',
					'affect_on_change_rule' => 'padding-left',
					'section' => 'styling',
					'tab' => __( 'Field > Padding', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Right', 'dslc_string' ),
					'id' => 'css_form_field_padding_right',
					'std' => '', // 'std' => '30',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap',
					'affect_on_change_rule' => 'padding-right',
					'section' => 'styling',
					'tab' => __( 'Field > Padding', 'dslc_string' ),
					'ext' => 'px',
				),


				
		/*
				array(
					'label' => __( 'BG Color', 'dslc_string' ),
					'id' => 'css_labels_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_labels_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_labels_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_labels_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius', 'dslc_string' ),
					'id' => 'css_labels_border_radius',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'border-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
		*/


				/**
				 * Labels
				 */

				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_labels_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_labels_font_size',
					'std' => '', // 'std' => '16',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_labels_font_weight',
					'std' => '', // 'std' => '300',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_labels_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_labels_line_height',
					'std' => '', // 'std' => '24',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
					'ext' => 'px'
				),
				/*
				array(
					'label' => __( 'Vertical Shift', 'dslc_string' ),
					'id' => 'css_labels_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => 'label',
					'affect_on_change_rule' => 'top',
					'section' => 'styling',
					'tab' => __( 'Field > Label', 'dslc_string' ),
					'ext' => 'px',
					'min' => -20,
					'max' => 20,
					'increment' => 1
				),
				*/
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_labels_margin_bottom',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_labels_padding_vertical',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_labels_padding_horizontal',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap > label',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Label', 'dslc_string' ),
				),

				/**
				 * Description
				 */

				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_description_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_description_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_description_font_weight',
					'std' => '', // 'std' => '400',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_description_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_description_line_height',
					'std' => '', // 'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Margin Top', 'dslc_string' ),
					'id' => 'css_description_margin_top',
					'std' => '', // 'std' => '25',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description',
					'affect_on_change_rule' => 'margin-top',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom ( paragraph )', 'dslc_string' ),
					'id' => 'css_description_margin_bottom',
					'std' => '', // 'std' => '25',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_description_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-description p',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Field > Description', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),

				/**
				 * Form Error
				 */

				array(
					'label' => __( 'BG Color', 'dslc_string' ),
					'id' => 'css_form_error_bg_color',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_form_error_border_color',
					'std' => '', // 'std' => '#5890e5',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_form_error_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_form_error_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius: Top', 'dslc_string' ),
					'id' => 'css_form_error_border_radius_top',
					'std' => '', // 'std' => '3',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius: Bottom', 'dslc_string' ),
					'id' => 'css_form_error_border_radius_bottom',
					'std' => '', // 'std' => '3',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_form_error_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_form_error_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_form_error_font_weight',
					'std' => '', // 'std' => '400',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_form_error_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_form_error_line_height',
					'std' => '', // 'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Vertical Shift', 'dslc_string' ),
					'id' => 'css_form_error_bottom',
					'std' => '', // 'std' => '25',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'bottom',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
					'ext' => 'px',
					'min' => -30,
					'max' => 0,
					'increment' => 1
				),

				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_form_error_padding_vertical',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_form_error_padding_horizontal',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Field > Error', 'dslc_string' ),
				),
				/*
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_form_error_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-field-error',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Field > Error', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),
				*/			


/**
 * ----------------------------------------------------------------------
 * 
 */

				array(
					'label' => ' ',
					'id' => 'pseudo_element2',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '',
					'affect_on_change_rule' => '',
					'section' => 'styling',
					'tab' => ' <br />',
					'ext' => 'px',
				),


/**
 * ----------------------------------------------------------------------
 * 
 */	
				
				/**
				 * Required
				 */
				array(
					'label' => __( 'Required fields message', 'dslc_string' ),
					'id' => 'css_req_items_display',
					'std' => '', // 'std' => 'block',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Show', 'dslc_string' ),
							'value' => 'block'
						),
						array(
							'label' => __( 'Hide', 'dslc_string' ),
							'value' => 'none'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'display',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_req_items_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_req_items_font_size',
					'std' => '', // 'std' => '14',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_req_items_font_weight',
					'std' => '', // 'std' => '300',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_req_items_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_req_items_line_height',
					'std' => '', // 'std' => '21',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_req_items_margin_bottom',
					'std' => '', // 'std' => '20',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_req_items_padding_vertical',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_req_items_padding_horizontal',
					'std' => '',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_req_items_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-required-items',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),

				/**
				 * Required Symbol
				 */
				
				array(
					'label' => __( 'Required Symbol (*)', 'dslc_string' ),
					'id' => 'css_symbol_display',
					'std' => '', // 'std' => 'inline',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Show', 'dslc_string' ),
							'value' => 'inline'
						),
						array(
							'label' => __( 'Hide', 'dslc_string' ),
							'value' => 'none'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.field-wrap .ninja-forms-req-symbol',
					'affect_on_change_rule' => 'display',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),
				array(
					'label' => __( 'Color', 'dslc_string' ),
					'id' => 'css_symbol_color',
					'std' => '', // 'std' => 'rgb(244, 95, 95)',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-req-symbol, .ninja-forms-req-symbol *',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Required', 'dslc_string' ),
				),

				/**
				 * Form Error Message
				 */

				array(
					'label' => __( ' BG Color', 'dslc_string' ),
					'id' => 'css_form_error_msg_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg, .ninja-forms-error-msg > *',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_form_error_msg_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_form_error_msg_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_form_error_msg_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Top', 'dslc_string' ),
					'id' => 'css_form_error_msg_border_radius_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
					'id' => 'css_form_error_msg_border_radius_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_form_error_msg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg, .ninja-forms-error-msg *',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_form_error_msg_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg, .ninja-forms-error-msg *',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_form_error_msg_font_weight',
					'std' => '', // 'std' => '400',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_form_error_msg_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_form_error_msg_line_height',
					'std' => '', // 'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_form_error_msg_margin_bottom',
					'std' => '', // 'std' => '25',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_form_error_msg_padding_vertical',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_form_error_msg_padding_horizontal',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_form_error_msg_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-error-msg',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Form Message > Error', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),

				/**
				 * Form Success Message
				 */

				array(
					'label' => __( ' BG Color', 'dslc_string' ),
					'id' => 'css_form_success_msg_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg, .ninja-forms-success-msg > *',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Color', 'dslc_string' ),
					'id' => 'css_form_success_msg_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Width', 'dslc_string' ),
					'id' => 'css_form_success_msg_border_width',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Borders', 'dslc_string' ),
					'id' => 'css_form_success_msg_border_trbl',
					'std' => '', // 'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'dslc_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'dslc_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Top', 'dslc_string' ),
					'id' => 'css_form_success_msg_border_radius_top',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
					'id' => 'css_form_success_msg_border_radius_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Color', 'dslc_string' ),
					'id' => 'css_form_success_msg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg, .ninja-forms-success-msg *',
					'affect_on_change_rule' => 'color',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Font Size', 'dslc_string' ),
					'id' => 'css_form_success_msg_font_size',
					'std' => '', // 'std' => '13',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg, .ninja-forms-success-msg *',
					'affect_on_change_rule' => 'font-size',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Font Weight', 'dslc_string' ),
					'id' => 'css_form_success_msg_font_weight',
					'std' => '', // 'std' => '400',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'font-weight',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
					'ext' => '',
					'min' => 100,
					'max' => 900,
					'increment' => 100
				),
				array(
					'label' => __( 'Font Family', 'dslc_string' ),
					'id' => 'css_form_success_msg_font_family',
					'std' => '',
					'type' => 'font',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'font-family',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Line Height', 'dslc_string' ),
					'id' => 'css_form_success_msg_line_height',
					'std' => '', // 'std' => '22',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'line-height',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
					'ext' => 'px'
				),
				array(
					'label' => __( 'Padding Vertical', 'dslc_string' ),
					'id' => 'css_form_success_msg_padding_vertical',
					'std' => '', // 'std' => '10',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Padding Horizontal', 'dslc_string' ),
					'id' => 'css_form_success_msg_padding_horizontal',
					'std' => '', // 'std' => '15',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
				),
				array(
					'label' => __( 'Text Align', 'dslc_string' ),
					'id' => 'css_form_success_msg_text_align',
					'std' => '', // 'std' => 'left',
					'type' => 'select',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-success-msg',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
					'tab' => __( 'Form Message > Success', 'dslc_string' ),
					'choices' => array(
						array(
							'label' => __( 'Left', 'dslc_string' ),
							'value' => 'left',
						),
						array(
							'label' => __( 'Center', 'dslc_string' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Right', 'dslc_string' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Justify', 'dslc_string' ),
							'value' => 'justify',
						),
					)
				),
				
				

				/**
				 * Responsive Tablet
				 */
				/*
				array(
					'label' => __( 'Responsive Styling', 'dslc_string' ),
					'id' => 'css_res_t',
					'std' => '', // 'std' => 'disabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Disabled', 'dslc_string' ),
							'value' => 'disabled'
						),
						array(
							'label' => __( 'Enabled', 'dslc_string' ),
							'value' => 'enabled'
						),
					),
					'section' => 'responsive',
					'tab' => __( 'tablet', 'dslc_string' ),
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_res_t_margin_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'tablet', 'dslc_string' ),
					'ext' => 'px',
				),
				*/

				/**
				 * Responsive Phone
				 */
				/*
				array(
					'label' => __( 'Responsive Styling', 'dslc_string' ),
					'id' => 'css_res_p',
					'std' => '', // 'std' => 'disabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Disabled', 'dslc_string' ),
							'value' => 'disabled'
						),
						array(
							'label' => __( 'Enabled', 'dslc_string' ),
							'value' => 'enabled'
						),
					),
					'section' => 'responsive',
					'tab' => __( 'phone', 'dslc_string' ),
				),
				array(
					'label' => __( 'Margin Bottom', 'dslc_string' ),
					'id' => 'css_res_ph_margin_bottom',
					'std' => '', // 'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.ninja-forms-cont',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'responsive',
					'tab' => __( 'phone', 'dslc_string' ),
					'ext' => 'px',
				),
				*/

			);// $dslc_options = array(

			// $dslc_options = array_merge( $dslc_options, $this->shared_options('animation_options') );
			// $dslc_options = array_merge( $dslc_options, $this->presets_options() );

			return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

		}//function options() 

		function output( $options ) {			
			global $dslc_active;

			$dslc_is_admin = ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) ) ? true : false;
						
			$this->module_start( $options );

			if ( ! isset( $options['ninjaform_title'] ) || $options['ninjaform_title'] == 'not_set' ) {
				if ( $dslc_is_admin ) :
					?><div class="dslc-notification dslc-red"><?php _e( 'Click the cog icon on the right of this box to choose which form to show.', 'dslc_string' ); ?> <span class="dslca-module-edit-hook dslc-icon dslc-icon-cog"></span></span></div><?php
				endif;

			} else {
				if( function_exists( 'ninja_forms_display_form' ) ){ 
					$form = '';
					$form_id = lbmn_get_ninjaform_id_by_title( $options['ninjaform_title'] );
					
					$form .= ninja_forms_return_echo('ninja_forms_display_form', $form_id);


					if ( ($form == '') && $dslc_is_admin ) :
						// Form title changed or other problem
						?><div class="dslc-notification dslc-red"><?php echo __( 'There is no form with title: ', 'dslc_string' ) . '<strong>"' . $options['ninjaform_title']  . '"</strong>. ' . __( 'Select a new form title if you rename it.', 'dslc_string' ); ?> <span class="dslca-module-edit-hook dslc-icon dslc-icon-cog"></span></span></div><?php
					else:
						// All fine do form output
						echo $form;
					endif;

					//ninja_forms_display_form( lbmn_get_ninjaform_id_by_title( $options['ninjaform_title'] ) ); 
				}
			}

			$this->module_end( $options );
		}
	} // class LBMN_Ninja_Forms



	/**
	 * ----------------------------------------------------------------------
	 * Set default values for Ninja Forms Module in the Live Composer
	 */

	add_filter( 'dslc_module_options', 'lbmn_alter_nf_defaults_in_lc', 10, 2 );
	function lbmn_alter_nf_defaults_in_lc( $options, $id ) {
		// The array that will hold new defaults
		$new_defaults = array();

		if ( $id == 'LBMN_Ninja_Forms' ) { 
			$new_defaults = array(
				'css_margin_bottom' => '40',
				'css_main_border_width' => '0',
				'css_form_field_padding_right' => '30',
				'css_form_field_padding_bottom' => '20',

				'css_labels_font_size' => '16',
				'css_labels_font_weight' => '300',

				'css_inputs_border_color' => 'rgb(220, 221, 221)',
				'css_inputs_color' => 'rgb(172, 174, 174)',
				'css_inputs_focus_border_color' => 'rgb(90, 173, 225)',
				'css_inputs_focus_txt_color' => 'rgb(60, 60, 60)',
				'css_inputs_border_width' => '1',
				'css_inputs_border_trbl' => 'top right bottom left ',
				'css_inputs_border_radius' => '4',
				'css_inputs_font_size' => '16',
				'css_inputs_line_height' => '24',
				'css_inputs_line_height' => '24',
				'css_inputs_padding_vertical' => '10',
				'css_inputs_padding_horizontal' => '12',
				'css_inputs_margin_bottom' => '0',



				'css_button_bg_color' => 'rgb(90, 173, 225)',
				'css_button_color' => 'rgb(255, 255, 255)',
				'css_button_bg_color_hover' => 'rgb(77, 125, 192)',
				'css_button_border_width' => '0',
				'css_button_border_trbl' => 'top right bottom left ',
				'css_button_border_radius' => '4',
				'css_button_font_size' => '18',
				'css_button_font_weight' => '300',
				'css_button_line_height' => '21',
				'css_button_padding_vertical' => '14',
				'css_button_padding_horizontal' => '20',
				'css_button_margin_top' => '15',

				'css_req_items_color' => 'rgb(165, 165, 165)',
				'css_req_items_font_size' => '14',
				'css_req_items_line_height' => '21',
				'css_req_items_margin_bottom' => '30',
				'css_req_items_padding_vertical' => '15',
				'css_symbol_color' => 'rgb(244, 133, 27)',

				'css_text_color' => 'rgb(165, 165, 165)',
				'css_text_font_size' => '14',
				'css_text_line_height' => '21',

				'css_description_color' => 'rgb(165, 165, 165)',
				'css_description_font_size' => '12',
				'css_description_line_height' => '18',
				'css_description_margin_top' => '10',

				'css_form_error_bg_color' => 'rgb(252, 9, 27)',
				'css_form_error_border_radius_top' => '0',
				'css_form_error_border_radius_bottom' => '4',
				'css_form_error_color' => 'rgb(255, 255, 255)',
				'css_form_error_font_size' => '12',
				'css_form_error_line_height' => '14',
				'css_form_error_bottom' => '0',
				'css_form_error_padding_vertical' => '4',
				'css_form_error_padding_horizontal' => '12',

				'css_form_success_msg_bg_color' => 'rgb(245, 248, 235)',
				'css_form_success_msg_border_color' => 'rgb(217, 223, 195)',
				'css_form_success_msg_border_width' => '1',
				'css_form_success_msg_border_trbl' => 'bottom ',
				'css_form_success_msg_border_radius_top' => '4',
				'css_form_success_msg_border_radius_bottom' => '4',
				'css_form_success_msg_color' => 'rgb(145, 177, 40)',
				'css_form_success_msg_font_size' => '21',
				'css_form_success_msg_line_height' => '30',
				'css_form_success_msg_padding_vertical' => '30',
				'css_form_success_msg_text_align' => 'center',

				'css_checkbox_labels_font_size' => '16',
				'css_checkbox_labels_font_weight' => '300',
				'css_checkbox_input_margin_right' => '6',

				'css_select_border_color' => 'rgb(220, 221, 221)',
				'css_select_border_width' => '1',
				'css_select_border_trbl' => 'top right bottom left ',
				'css_select_padding_vertical' => '9',
				'css_select_padding_horizontal' => '14',

				'css_hr_bg_color' => 'rgba(220, 221, 221, 0.48)',
				'css_hr_height' => '1',
				'css_hr_margin_top' => '20',
				'css_hr_margin_bottom' => '15',


			);
		}

		// Call the function that alters the defaults and return
		return dslc_set_defaults( $new_defaults, $options );
	}


	function lbmn_ninja_forms_custom_display_before_field( $field_id, $data ){
		// do_action( 'add_debug_info', $data, 'Field id: ' . $field_id ); // for debug
		
		// Wrap HR with extra div for esier styling
		if ( $data['label'] == 'hr') {
			echo '<div class="field-wrap hr-wrap">';
		}
	}
	add_action( 'ninja_forms_display_before_field', 'lbmn_ninja_forms_custom_display_before_field', 10, 2 );


	function lbmn_ninja_forms_custom_display_after_field( $field_id, $data ){
		// do_action( 'add_debug_info', $data, 'Field id: ' . $field_id ); // for debug
		
		// Wrap HR with extra div for esier styling
		if ( $data['label'] == 'hr') {
			echo '</div>';
		}
	}
	add_action( 'ninja_forms_display_after_field', 'lbmn_ninja_forms_custom_display_after_field', 10, 2 );




} //defined('NF_PLUGIN_VERSION')


