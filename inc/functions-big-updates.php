<?php
/**
 * Custom functions that do some stuff during complex/big updates
 *
 * -------------------------------------------------------------------
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* ----------------------------------------------------------------------
* Return menu id by it's title
*/
if ( ! function_exists( 'lbmn_on_theme_activation' ) ) {

	add_action("after_switch_theme", "lbmn_on_theme_activation", 10, 2);
	function lbmn_on_theme_activation() {

		/**
		 * ----------------------------------------------------------------------
		 * Save theme updates history
		 */

		// Check theme version
		$lbmn_theme = wp_get_theme();
		$curent_theme_ver = $lbmn_theme->get( 'Version' );
		$theme_ver_log = get_option( 'lbmn_theme_updates_log');

		if ( ! $theme_ver_log ) {
			$theme_ver_log = array();
		}

		// This code marks the very first theme installation
		// when no theme version control were available
		if ( get_option( LBMN_THEME_NAME . '_basic_setup_done') && ! in_array('1.0.1', $theme_ver_log) ) {
			array_unshift($theme_ver_log, '1.0.1');
			update_option( 'lbmn_theme_updates_log', $theme_ver_log);
		}

		if ( get_option( LBMN_THEME_NAME . '_basic_setup_done') ) {
			update_option( LBMN_THEME_NAME . '_basic_config_done', true);
		}

		// Add new theme version into the log
		if ( ! in_array($curent_theme_ver, $theme_ver_log) ) {
			array_unshift($theme_ver_log, $curent_theme_ver);
			update_option( 'lbmn_theme_updates_log', $theme_ver_log);
		}

		if ( get_option( LBMN_THEME_NAME . '_basic_config_done') && !defined('LBMN_THEME_CONFUGRATED') ) {
			define ('LBMN_THEME_CONFUGRATED', true);
		}

		/**
		 * ----------------------------------------------------------------------
		 */

		$current_ver_above_181 = version_compare($curent_theme_ver, '1.8.1') >= 0;
		$theme_update_ver_181_compelted = get_option( 'lbmn_update_ver_181' , 0);
		$has_ver_below_181 = false; // theme history has ver 1.8 or below

		foreach ($theme_ver_log as $ver) {

			// if version history has 1.8 or below
			if (version_compare($ver, '1.8') <= 0) {
				$has_ver_below_181 = true;
			}

		}

		/**
		 * ----------------------------------------------------------------------
		 */

		$theme_were_updated = count($theme_ver_log) > 1;

		// $latest_version = array_shift(array_values($theme_ver_log));

		// If updated to verison 1.8.1 from previous version run migration code
		if ( $current_ver_above_181 && $has_ver_below_181 && $theme_were_updated && !$theme_update_ver_181_compelted  ) {
			lbmn_theme_update_181();
		}

	}
}

add_action('admin_notices', 'lbmn_admin_notices');
function lbmn_admin_notices() {
  if ($notices= get_option('lbmn_deferred_admin_notices')) {
    foreach ($notices as $notice) {
      echo "<div class='updated'><p>$notice</p></div>";
    }
    delete_option('lbmn_deferred_admin_notices');
  }
}

/**
 * ----------------------------------------------------------------------
 * Complex theme update ver 1.8.1
 * LC – System Templates recreation
 * LC – Migrate to native archive/search/author templates
 */
function lbmn_theme_update_181() {

	$notices= get_option('lbmn_deferred_admin_notices', array());
	$notices[]= '<br /><span class="dashicons dashicons-warning"></span> <strong>Please read:</strong> SEOWP theme ver.1.8.1 can\'t be updated properly without some important actions. <br /><br /><a href="http://docs.lumbermandesigns.com/article/82-theme-update-to-version-1-8-1" class="button button-primary" target="_blank" style="text-decoration:none">Read update instrucitons</a><br /><br />';
	update_option('lbmn_deferred_admin_notices', $notices);


	$current_lc_archive_options = get_option('dslc_plugin_options_archives');

	// Update 404 template id in the Live Composer settings
	// from the Theme Customizer
	$template_404_post_id = get_theme_mod( 'lbmn_systempage_404', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' ) );

	if ( $template_404_post_id ) {
		$current_lc_archive_options['404_page'] = $template_404_post_id;
	}

	// Create new archive listing template

	$new_archive_listing_code = '[dslc_modules_section show_on="desktop tablet phone" type="wrapped" columns_spacing="spacing" bg_color="rgb(241, 241, 241)" bg_image_thumb="disabled" bg_image="" bg_image_repeat="repeat" bg_image_position="left top" bg_image_attachment="scroll" bg_image_size="auto" bg_video="" bg_video_overlay_color="#000000" bg_video_overlay_opacity="0" border_color="" border_width="0" border_style="solid" border="top bottom" margin_h="0" margin_b="0" padding="0" padding_h="0" custom_class="blog-noauthor blog-balanced-thumbnail blog-add-icons" custom_id="" ] [dslc_modules_area last="yes" first="no" size="12"] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMTUiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoyOiIxNSI7czoxODoibW9kdWxlX2luc3RhbmNlX2lkIjtpOjIwO3M6NzoicG9zdF9pZCI7czozOiI4MTYiO3M6OToibW9kdWxlX2lkIjtzOjE0OiJEU0xDX1NlcGFyYXRvciI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjt9[/dslc_module] [dslc_module last="yes"]YTo3OntzOjc6ImNvbnRlbnQiO3M6MzE6IjxoMT5bbGJtbl9hcmNoaXZlX2hlYWRpbmddPC9oMT4iO3M6MjA6ImNzc19oMV9tYXJnaW5fYm90dG9tIjtzOjE6IjAiO3M6MTg6Im1vZHVsZV9pbnN0YW5jZV9pZCI7aToyMTtzOjc6InBvc3RfaWQiO3M6MzoiNTg3IjtzOjExOiJkc2xjX21fc2l6ZSI7czoyOiIxMiI7czo5OiJtb2R1bGVfaWQiO3M6MTY6IkRTTENfVGV4dF9TaW1wbGUiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjt9[/dslc_module] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMjAiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoxOiIxIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO2k6MjI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6OToibW9kdWxlX2lkIjtzOjE0OiJEU0xDX1NlcGFyYXRvciI7czoxNjoiZHNsY19tX3NpemVfbGFzdCI7czozOiJ5ZXMiO30=[/dslc_module] [dslc_module last="yes"]YToxMzA6e3M6MTE6Im9yaWVudGF0aW9uIjtzOjEwOiJob3Jpem9udGFsIjtzOjE1OiJwYWdpbmF0aW9uX3R5cGUiO3M6ODoibnVtYmVyZWQiO3M6NzoiY29sdW1ucyI7czoyOiIxMiI7czoxMzoicG9zdF9lbGVtZW50cyI7czozNjoidGh1bWJuYWlsIHRpdGxlIG1ldGEgZXhjZXJwdCBidXR0b24gIjtzOjIwOiJjc3Nfd3JhcHBlcl9iZ19jb2xvciI7czoxMToidHJhbnNwYXJlbnQiO3M6MjQ6ImNzc193cmFwcGVyX2JvcmRlcl9jb2xvciI7czoxMToidHJhbnNwYXJlbnQiO3M6MTQ6ImNzc19zZXBfaGVpZ2h0IjtzOjI6IjEwIjtzOjEzOiJjc3Nfc2VwX3N0eWxlIjtzOjQ6Im5vbmUiO3M6MTg6ImNzc190aHVtYl9iZ19jb2xvciI7czoxODoicmdiKDI1NSwgMjU1LCAyNTUpIjtzOjI3OiJjc3NfdGh1bWJfYm9yZGVyX3JhZGl1c190b3AiO3M6MToiMCI7czoxODoidGh1bWJfbWFyZ2luX3JpZ2h0IjtzOjE6IjAiO3M6MTk6InRodW1iX3Jlc2l6ZV9oZWlnaHQiO3M6MzoiNDAwIjtzOjI1OiJ0aHVtYl9yZXNpemVfd2lkdGhfbWFudWFsIjtzOjM6IjYwMCI7czoxODoidGh1bWJfcmVzaXplX3dpZHRoIjtzOjI6IjYyIjtzOjExOiJ0aHVtYl93aWR0aCI7czoyOiI0MCI7czoxNzoiY3NzX21haW5fYmdfY29sb3IiO3M6MTg6InJnYigyNTUsIDI1NSwgMjU1KSI7czoyMToiY3NzX21haW5fYm9yZGVyX2NvbG9yIjtzOjExOiJ0cmFuc3BhcmVudCI7czoyMToiY3NzX21haW5fYm9yZGVyX3dpZHRoIjtzOjE6IjAiO3M6MjA6ImNzc19tYWluX2JvcmRlcl90cmJsIjtzOjA6IiI7czoyOToiY3NzX21haW5fYm9yZGVyX3JhZGl1c19ib3R0b20iO3M6MToiMCI7czoyNToiY3NzX21haW5fcGFkZGluZ192ZXJ0aWNhbCI7czoyOiI0MCI7czoyNzoiY3NzX21haW5fcGFkZGluZ19ob3Jpem9udGFsIjtzOjI6IjUwIjtzOjE5OiJjc3NfbWFpbl9taW5faGVpZ2h0IjtzOjM6IjMyMSI7czoxOToiY3NzX21haW5fdGV4dF9hbGlnbiI7czo0OiJsZWZ0IjtzOjEzOiJtYWluX3Bvc2l0aW9uIjtzOjc6InRvcGxlZnQiO3M6MTc6InRpdGxlX2NvbG9yX2hvdmVyIjtzOjE3OiJyZ2IoOTMsIDE0NCwgMjI2KSI7czoxNToidGl0bGVfZm9udF9zaXplIjtzOjI6IjMwIjtzOjIxOiJjc3NfdGl0bGVfZm9udF93ZWlnaHQiO3M6MzoiMzAwIjtzOjE3OiJ0aXRsZV9saW5lX2hlaWdodCI7czoyOiIzOCI7czoxMjoidGl0bGVfbWFyZ2luIjtzOjE6IjUiO3M6MjE6ImNzc19tZXRhX2JvcmRlcl9jb2xvciI7czoxODoicmdiKDI0MywgMjQzLCAyNDMpIjtzOjIwOiJjc3NfbWV0YV9ib3JkZXJfdHJibCI7czowOiIiO3M6MTg6ImNzc19tZXRhX2ZvbnRfc2l6ZSI7czoyOiIxNyI7czoyMDoiY3NzX21ldGFfZm9udF9mYW1pbHkiO3M6MDoiIjtzOjIwOiJjc3NfbWV0YV9mb250X3dlaWdodCI7czozOiIzMDAiO3M6MjA6ImNzc19tZXRhX2xpbmVfaGVpZ2h0IjtzOjI6IjI0IjtzOjIyOiJjc3NfbWV0YV9tYXJnaW5fYm90dG9tIjtzOjI6IjE4IjtzOjI1OiJjc3NfbWV0YV9wYWRkaW5nX3ZlcnRpY2FsIjtzOjE6IjAiO3M6MTk6ImNzc19tZXRhX2xpbmtfY29sb3IiO3M6MTc6InJnYig5MywgMTQ0LCAyMjYpIjtzOjI4OiJjc3NfbWV0YV9hdmF0YXJfbWFyZ2luX3JpZ2h0IjtzOjE6IjgiO3M6MjA6ImNzc19tZXRhX2F2YXRhcl9zaXplIjtzOjI6IjIzIjtzOjE3OiJjc3NfZXhjZXJwdF9jb2xvciI7czoxODoicmdiKDEzMCwgMTM0LCAxMzgpIjtzOjIxOiJjc3NfZXhjZXJwdF9mb250X3NpemUiO3M6MjoiMTciO3M6MjM6ImNzc19leGNlcnB0X2ZvbnRfd2VpZ2h0IjtzOjM6IjMwMCI7czoyMzoiY3NzX2V4Y2VycHRfbGluZV9oZWlnaHQiO3M6MjoiMjYiO3M6MTQ6ImV4Y2VycHRfbWFyZ2luIjtzOjI6IjMwIjtzOjE0OiJleGNlcnB0X2xlbmd0aCI7czoyOiIyMyI7czoxMToiYnV0dG9uX3RleHQiO3M6MTY6IkNvbnRpbnVlIHJlYWRpbmciO3M6MTk6ImNzc19idXR0b25fYmdfY29sb3IiO3M6MTE6InRyYW5zcGFyZW50IjtzOjIzOiJjc3NfYnV0dG9uX2JvcmRlcl93aWR0aCI7czoxOiIxIjtzOjIzOiJjc3NfYnV0dG9uX2JvcmRlcl9jb2xvciI7czoxODoicmdiKDI0MiwgMjQyLCAyNDIpIjtzOjI5OiJjc3NfYnV0dG9uX2JvcmRlcl9jb2xvcl9ob3ZlciI7czoxNzoicmdiKDc1LCAxMjMsIDE5NCkiO3M6MTY6ImNzc19idXR0b25fY29sb3IiO3M6MTc6InJnYigzOCwgMTQ5LCAyMjMpIjtzOjIwOiJjc3NfYnV0dG9uX2ZvbnRfc2l6ZSI7czoyOiIxNCI7czoyMjoiY3NzX2J1dHRvbl9mb250X3dlaWdodCI7czozOiIzMDAiO3M6MjI6ImNzc19idXR0b25fZm9udF9mYW1pbHkiO3M6NjoiUm9ib3RvIjtzOjI5OiJjc3NfYnV0dG9uX3BhZGRpbmdfaG9yaXpvbnRhbCI7czoyOiIxNSI7czo5OiJjc3NfcmVzX3QiO3M6NzoiZW5hYmxlZCI7czoyMDoiY3NzX3Jlc190X3NlcF9oZWlnaHQiO3M6MjoiMTQiO3M6Mjg6ImNzc19yZXNfdF90aHVtYl9tYXJnaW5fcmlnaHQiO3M6MToiMCI7czozMjoiY3NzX3Jlc190X3RodW1iX3BhZGRpbmdfdmVydGljYWwiO3M6MjoiMTUiO3M6MzQ6ImNzc19yZXNfdF90aHVtYl9wYWRkaW5nX2hvcml6b250YWwiO3M6MjoiMTEiO3M6MzE6ImNzc19yZXNfdF9tYWluX3BhZGRpbmdfdmVydGljYWwiO3M6MjoiMTIiO3M6MzM6ImNzc19yZXNfdF9tYWluX3BhZGRpbmdfaG9yaXpvbnRhbCI7czoyOiIxNCI7czoyNToiY3NzX3Jlc190X3RpdGxlX2ZvbnRfc2l6ZSI7czoyOiIyNSI7czoyNzoiY3NzX3Jlc190X3RpdGxlX2xpbmVfaGVpZ2h0IjtzOjI6IjMyIjtzOjIyOiJjc3NfcmVzX3RfdGl0bGVfbWFyZ2luIjtzOjE6IjQiO3M6MjQ6ImNzc19yZXNfdF9tZXRhX2ZvbnRfc2l6ZSI7czoyOiIxNSI7czoyODoiY3NzX3Jlc190X21ldGFfbWFyZ2luX2JvdHRvbSI7czoyOiIyMCI7czozMToiY3NzX3Jlc190X21ldGFfcGFkZGluZ192ZXJ0aWNhbCI7czoxOiIwIjtzOjI3OiJjc3NfcmVzX3RfZXhjZXJwdF9mb250X3NpemUiO3M6MjoiMTUiO3M6Mjk6ImNzc19yZXNfdF9leGNlcnB0X2xpbmVfaGVpZ2h0IjtzOjI6IjIyIjtzOjI0OiJjc3NfcmVzX3RfZXhjZXJwdF9tYXJnaW4iO3M6MjoiMjAiO3M6MjY6ImNzc19yZXNfdF9idXR0b25fZm9udF9zaXplIjtzOjI6IjEzIjtzOjk6ImNzc19yZXNfcCI7czo3OiJlbmFibGVkIjtzOjIwOiJjc3NfcmVzX3Bfc2VwX2hlaWdodCI7czoxOiIxIjtzOjI4OiJjc3NfcmVzX3BfdGh1bWJfbWFyZ2luX3JpZ2h0IjtzOjE6IjAiO3M6MzI6ImNzc19yZXNfcF90aHVtYl9wYWRkaW5nX3ZlcnRpY2FsIjtzOjI6IjI1IjtzOjM0OiJjc3NfcmVzX3BfdGh1bWJfcGFkZGluZ19ob3Jpem9udGFsIjtzOjI6IjI1IjtzOjMxOiJjc3NfcmVzX3BfbWFpbl9wYWRkaW5nX3ZlcnRpY2FsIjtzOjI6IjE5IjtzOjMzOiJjc3NfcmVzX3BfbWFpbl9wYWRkaW5nX2hvcml6b250YWwiO3M6MToiNiI7czoyNzoiY3NzX3Jlc19wX3RpdGxlX2xpbmVfaGVpZ2h0IjtzOjI6IjI0IjtzOjIyOiJjc3NfcmVzX3BfdGl0bGVfbWFyZ2luIjtzOjE6IjIiO3M6MjQ6ImNzc19yZXNfcF9tZXRhX2ZvbnRfc2l6ZSI7czoyOiIxNCI7czoyODoiY3NzX3Jlc19wX21ldGFfbWFyZ2luX2JvdHRvbSI7czoxOiI4IjtzOjMxOiJjc3NfcmVzX3BfbWV0YV9wYWRkaW5nX3ZlcnRpY2FsIjtzOjE6IjAiO3M6Mjc6ImNzc19yZXNfcF9leGNlcnB0X2ZvbnRfc2l6ZSI7czoyOiIxNCI7czoyOToiY3NzX3Jlc19wX2V4Y2VycHRfbGluZV9oZWlnaHQiO3M6MjoiMjEiO3M6MjQ6ImNzc19yZXNfcF9leGNlcnB0X21hcmdpbiI7czoyOiIyMCI7czoyNjoiY3NzX3Jlc19wX2J1dHRvbl9mb250X3NpemUiO3M6MjoiMTIiO3M6MTg6Im1haW5faGVhZGluZ190aXRsZSI7czo0MDoiTGF0ZXN0IGNvbXBhbnkgdXBkYXRlcyBhbmQgaW5kdXN0cnkgbmV3cyI7czoyMzoibWFpbl9oZWFkaW5nX2xpbmtfdGl0bGUiO3M6MjY6IlN1YnNjcmliZSB0byBlbWFpbCB1cGRhdGVzIjtzOjIyOiJjc3NfbWFpbl9oZWFkaW5nX2NvbG9yIjtzOjE1OiJyZ2IoNjYsIDcyLCA3OCkiO3M6MjY6ImNzc19tYWluX2hlYWRpbmdfZm9udF9zaXplIjtzOjI6IjI3IjtzOjI4OiJjc3NfbWFpbl9oZWFkaW5nX2ZvbnRfd2VpZ2h0IjtzOjM6IjMwMCI7czoyODoiY3NzX21haW5faGVhZGluZ19mb250X2ZhbWlseSI7czo2OiJSb2JvdG8iO3M6Mjg6ImNzc19tYWluX2hlYWRpbmdfbGluZV9oZWlnaHQiO3M6MjoiMzgiO3M6Mjc6ImNzc19tYWluX2hlYWRpbmdfbGlua19jb2xvciI7czoxNzoicmdiKDQyLCAxNjAsIDIzOSkiO3M6MzE6ImNzc19tYWluX2hlYWRpbmdfbGlua19mb250X3NpemUiO3M6MjoiMTUiO3M6MzM6ImNzc19tYWluX2hlYWRpbmdfbGlua19mb250X3dlaWdodCI7czozOiIzMDAiO3M6MzM6ImNzc19tYWluX2hlYWRpbmdfbGlua19mb250X2ZhbWlseSI7czo2OiJSb2JvdG8iO3M6MTM6InZpZXdfYWxsX2xpbmsiO3M6MTk6IiNlbWFpbC1zdWJzY3JpcHRpb24iO3M6MjY6ImNzc19tYWluX2hlYWRpbmdfc2VwX2NvbG9yIjtzOjE4OiJyZ2IoMTkzLCAxOTMsIDE5MykiO3M6MjU6ImNzc19oZWFkaW5nX21hcmdpbl9ib3R0b20iO3M6MjoiNDAiO3M6MzI6ImNzc19yZXNfdF9tYWluX2hlYWRpbmdfZm9udF9zaXplIjtzOjI6IjIzIjtzOjM0OiJjc3NfcmVzX3RfbWFpbl9oZWFkaW5nX2xpbmVfaGVpZ2h0IjtzOjI6IjM1IjtzOjM3OiJjc3NfcmVzX3RfbWFpbl9oZWFkaW5nX2xpbmtfZm9udF9zaXplIjtzOjI6IjE1IjtzOjMxOiJjc3NfcmVzX3RfaGVhZGluZ19tYXJnaW5fYm90dG9tIjtzOjI6IjMwIjtzOjMyOiJjc3NfcmVzX3BfbWFpbl9oZWFkaW5nX2ZvbnRfc2l6ZSI7czoyOiIyMyI7czozNDoiY3NzX3Jlc19wX21haW5faGVhZGluZ19saW5lX2hlaWdodCI7czoyOiIyNyI7czozNzoiY3NzX3Jlc19wX21haW5faGVhZGluZ19saW5rX2ZvbnRfc2l6ZSI7czoyOiIxNiI7czozOToiY3NzX3Jlc19wX21haW5faGVhZGluZ19saW5rX3BhZGRpbmdfdmVyIjtzOjI6IjE1IjtzOjMxOiJjc3NfcmVzX3BfaGVhZGluZ19tYXJnaW5fYm90dG9tIjtzOjE6IjAiO3M6MTk6ImNzc19maWx0ZXJfcG9zaXRpb24iO3M6NToicmlnaHQiO3M6MjQ6ImNzc19maWx0ZXJfbWFyZ2luX2JvdHRvbSI7czoyOiI2MCI7czoxMzoiY3NzX3BhZ19hbGlnbiI7czo2OiJjZW50ZXIiO3M6MjQ6ImNzc19wYWdfcGFkZGluZ192ZXJ0aWNhbCI7czoyOiIyMCI7czoyMToiY3NzX3BhZ19pdGVtX2JnX2NvbG9yIjtzOjE4OiJyZ2IoMjUzLCAyNTMsIDI1MykiO3M6MjU6ImNzc19wYWdfaXRlbV9ib3JkZXJfY29sb3IiO3M6MTg6InJnYigyNTUsIDI1NSwgMjU1KSI7czoyMjoiY3NzX3BhZ19pdGVtX2ZvbnRfc2l6ZSI7czoyOiIyMCI7czoyNDoiY3NzX3BhZ19pdGVtX2ZvbnRfd2VpZ2h0IjtzOjM6IjMwMCI7czoyNDoiY3NzX3BhZ19pdGVtX2ZvbnRfZmFtaWx5IjtzOjA6IiI7czoyOToiY3NzX3BhZ19pdGVtX3BhZGRpbmdfdmVydGljYWwiO3M6MjoiMTUiO3M6MzE6ImNzc19wYWdfaXRlbV9wYWRkaW5nX2hvcml6b250YWwiO3M6MjoiMjAiO3M6MjA6ImNzc19wYWdfaXRlbV9zcGFjaW5nIjtzOjI6IjE2IjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO2k6MjM7czo3OiJwb3N0X2lkIjtzOjM6IjU2OSI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6OToibW9kdWxlX2lkIjtzOjk6IkRTTENfQmxvZyI7czoxNjoiZHNsY19tX3NpemVfbGFzdCI7czozOiJ5ZXMiO30=[/dslc_module] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMTUiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoyOiIxNSI7czoxODoibW9kdWxlX2luc3RhbmNlX2lkIjtpOjI0O3M6NzoicG9zdF9pZCI7czozOiI1NjkiO3M6MTE6ImRzbGNfbV9zaXplIjtzOjI6IjEyIjtzOjk6Im1vZHVsZV9pZCI7czoxNDoiRFNMQ19TZXBhcmF0b3IiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjt9[/dslc_module] [/dslc_modules_area] [/dslc_modules_section]';

	$new_archive_listing_post = array(
	  'post_content' => '', // The full text of the post.
	  'post_name'    => 'archive-listing-template', // The name (slug) for your post
	  'post_title'   => LBMN_SYSTEMPAGE_ARCHIVE_DEFAULT, // The title of your post.
	  'post_status'  => 'publish', // Default 'draft'.
	  'post_type'    => 'lbmn_archive', // Default 'post'.
	);

	$new_archive_listing_id = wp_insert_post( $new_archive_listing_post);
	add_post_meta($new_archive_listing_id, 'dslc_code', $new_archive_listing_code);

	if ( $new_archive_listing_id ) {
		$current_lc_archive_options['post'] = $new_archive_listing_id;
		$current_lc_archive_options['dslc_projects'] = $new_archive_listing_id;
		$current_lc_archive_options['dslc_galleries'] = $new_archive_listing_id;
		$current_lc_archive_options['dslc_downloads'] = $new_archive_listing_id;
		$current_lc_archive_options['dslc_staff'] = $new_archive_listing_id;
		$current_lc_archive_options['dslc_partners'] = $new_archive_listing_id;
		$current_lc_archive_options['author'] = $new_archive_listing_id;
	}


	// Create new search results listing template

	$new_search_listing_code = '[dslc_modules_section show_on="desktop tablet phone" type="wrapped" columns_spacing="spacing" bg_color="rgb(249, 249, 249)" bg_image_thumb="disabled" bg_image="" bg_image_repeat="repeat" bg_image_position="left top" bg_image_attachment="scroll" bg_image_size="auto" bg_video="" bg_video_overlay_color="#000000" bg_video_overlay_opacity="0" border_color="rgb(241, 241, 241)" border_width="1" border_style="solid" border="top " margin_h="0" margin_b="0" padding="0" padding_h="0" custom_class="" custom_id="" ] [dslc_modules_area last="yes" first="no" size="12"] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMjAiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoxOiIxIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO3M6NDoiODE0MyI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czo5OiJtb2R1bGVfaWQiO3M6MTQ6IkRTTENfU2VwYXJhdG9yIjtzOjE2OiJkc2xjX21fc2l6ZV9sYXN0IjtzOjM6InllcyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO30=[/dslc_module] [/dslc_modules_area] [dslc_modules_area last="no" first="yes" size="2"] [dslc_module last="yes"]YTo4OntzOjU6InN0eWxlIjtzOjk6ImludmlzaWJsZSI7czo5OiJjc3NfcmVzX3AiO3M6NzoiZW5hYmxlZCI7czoxMjoicmVzX3BfaGVpZ2h0IjtzOjE6IjEiO3M6MTg6Im1vZHVsZV9pbnN0YW5jZV9pZCI7czo0OiIxMTQ3IjtzOjc6InBvc3RfaWQiO3M6MzoiNTg3IjtzOjk6Im1vZHVsZV9pZCI7czoxNDoiRFNMQ19TZXBhcmF0b3IiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjtzOjExOiJkc2xjX21fc2l6ZSI7czoyOiIxMiI7fQ==[/dslc_module] [/dslc_modules_area] [dslc_modules_area last="no" first="no" size="8"] [dslc_module last="yes"]YToxMTp7czo3OiJjb250ZW50IjtzOjM1MjoiPGZvcm0gcm9sZT1cInNlYXJjaFwiIGFjdGlvbj1cIi9cIiBjbGFzcz1cInNlYXJjaGZvcm1cIiBpZD1cInNlYXJjaGZvcm1cIiBtZXRob2Q9XCJnZXRcIj4KPGxhYmVsIGNsYXNzPVwic2NyZWVuLXJlYWRlci10ZXh0XCIgZm9yPVwic1wiPlNlYXJjaDwvbGFiZWw+CjxpbnB1dCB0eXBlPVwidGV4dFwiIHBsYWNlaG9sZGVyPVwiU2VhcmNoIOKAplwiIGlkPVwic1wiIHZhbHVlPVwiXCIgbmFtZT1cInNcIiBjbGFzcz1cImZpZWxkXCIgIHNpemU9XCI1MFwiPgo8aW5wdXQgdHlwZT1cInN1Ym1pdFwiIHZhbHVlPVwiU2VhcmNoXCIgaWQ9XCJzZWFyY2hzdWJtaXRcIiBjbGFzcz1cInN1Ym1pdCBidXR0b25cIj4KPC9mb3JtPiI7czoxMDoiY3NzX2N1c3RvbSI7czo3OiJlbmFibGVkIjtzOjI0OiJjc3NfaW5wdXRzX2JvcmRlcl9yYWRpdXMiO3M6MToiMyI7czoyNzoiY3NzX2lucHV0c19wYWRkaW5nX3ZlcnRpY2FsIjtzOjI6IjIwIjtzOjIwOiJjc3NfYnV0dG9uX2ZvbnRfc2l6ZSI7czoyOiIxNyI7czoyMjoiY3NzX2J1dHRvbl9mb250X3dlaWdodCI7czozOiIzMDAiO3M6Mjk6ImNzc19idXR0b25fcGFkZGluZ19ob3Jpem9udGFsIjtzOjI6IjIyIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO3M6NDoiMTE1NyI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6OToibW9kdWxlX2lkIjtzOjk6IkRTTENfSHRtbCI7fQ==[/dslc_module] [/dslc_modules_area] [dslc_modules_area last="no" first="yes" size="12"] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMjAiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoxOiIxIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO3M6NDoiODEzOCI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czo5OiJtb2R1bGVfaWQiO3M6MTQ6IkRTTENfU2VwYXJhdG9yIjtzOjE2OiJkc2xjX21fc2l6ZV9sYXN0IjtzOjM6InllcyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO30=[/dslc_module] [/dslc_modules_area] [/dslc_modules_section] [dslc_modules_section show_on="desktop tablet phone" type="wrapped" columns_spacing="spacing" bg_color="rgb(241, 241, 241)" bg_image_thumb="disabled" bg_image="" bg_image_repeat="repeat" bg_image_position="left top" bg_image_attachment="scroll" bg_image_size="auto" bg_video="" bg_video_overlay_color="#000000" bg_video_overlay_opacity="0" border_color="" border_width="0" border_style="solid" border="top bottom" margin_h="0" margin_b="0" padding="40" padding_h="0" custom_class="" custom_id="" ] [dslc_modules_area last="no" first="yes" size="2"] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MToiMSI7czo1OiJzdHlsZSI7czo5OiJpbnZpc2libGUiO3M6OToiY3NzX3Jlc190IjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc190X2hlaWdodCI7czoxOiIxIjtzOjk6ImNzc19yZXNfcCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfcF9oZWlnaHQiO3M6MToiMSI7czoxODoibW9kdWxlX2luc3RhbmNlX2lkIjtzOjQ6IjgxNDgiO3M6NzoicG9zdF9pZCI7czozOiI1ODciO3M6OToibW9kdWxlX2lkIjtzOjE0OiJEU0xDX1NlcGFyYXRvciI7czoxNjoiZHNsY19tX3NpemVfbGFzdCI7czozOiJ5ZXMiO3M6MTE6ImRzbGNfbV9zaXplIjtzOjI6IjEyIjt9[/dslc_module] [/dslc_modules_area] [dslc_modules_area last="no" first="no" size="8"] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMTEiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MToiMSI7czo5OiJjc3NfcmVzX3AiO3M6NzoiZW5hYmxlZCI7czoxMjoicmVzX3BfaGVpZ2h0IjtzOjE6IjEiO3M6MTg6Im1vZHVsZV9pbnN0YW5jZV9pZCI7czo0OiIxMTU4IjtzOjc6InBvc3RfaWQiO3M6MzoiNTg3IjtzOjk6Im1vZHVsZV9pZCI7czoxNDoiRFNMQ19TZXBhcmF0b3IiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjtzOjExOiJkc2xjX21fc2l6ZSI7czoyOiIxMiI7fQ==[/dslc_module] [dslc_module last="yes"]YTo3OntzOjc6ImNvbnRlbnQiO3M6MzE6IjxoMT5bbGJtbl9hcmNoaXZlX2hlYWRpbmddPC9oMT4iO3M6MjA6ImNzc19oMV9tYXJnaW5fYm90dG9tIjtzOjE6IjAiO3M6MTg6Im1vZHVsZV9pbnN0YW5jZV9pZCI7czoxOiI3IjtzOjc6InBvc3RfaWQiO3M6MzoiNTg3IjtzOjExOiJkc2xjX21fc2l6ZSI7czoyOiIxMiI7czo5OiJtb2R1bGVfaWQiO3M6MTY6IkRTTENfVGV4dF9TaW1wbGUiO3M6MTY6ImRzbGNfbV9zaXplX2xhc3QiO3M6MzoieWVzIjt9[/dslc_module] [dslc_module last="yes"]YToxMTp7czo2OiJoZWlnaHQiO3M6MjoiMzAiO3M6NToic3R5bGUiO3M6OToiaW52aXNpYmxlIjtzOjk6ImNzc19yZXNfdCI7czo3OiJlbmFibGVkIjtzOjEyOiJyZXNfdF9oZWlnaHQiO3M6MjoiMjAiO3M6OToiY3NzX3Jlc19wIjtzOjc6ImVuYWJsZWQiO3M6MTI6InJlc19wX2hlaWdodCI7czoxOiIxIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO3M6MToiOCI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6OToibW9kdWxlX2lkIjtzOjE0OiJEU0xDX1NlcGFyYXRvciI7czoxNjoiZHNsY19tX3NpemVfbGFzdCI7czozOiJ5ZXMiO30=[/dslc_module] [dslc_module last="yes"]YToxODp7czo0OiJ0eXBlIjtzOjQ6ImdyaWQiO3M6MTE6Im9yaWVudGF0aW9uIjtzOjEwOiJob3Jpem9udGFsIjtzOjY6ImFtb3VudCI7czoyOiIxMCI7czoxNToicGFnaW5hdGlvbl90eXBlIjtzOjg6Im51bWJlcmVkIjtzOjc6ImNvbHVtbnMiO3M6MjoiMTIiO3M6ODoiZWxlbWVudHMiO3M6MDoiIjtzOjEzOiJwb3N0X2VsZW1lbnRzIjtzOjE0OiJ0aXRsZSBleGNlcnB0ICI7czoyMDoiY3NzX3NlcF9ib3JkZXJfY29sb3IiO3M6MTg6InJnYigyMjcsIDIyNywgMjI3KSI7czoxMzoiY3NzX3NlcF9zdHlsZSI7czo1OiJzb2xpZCI7czoxMToidGh1bWJfd2lkdGgiO3M6MjoiNDAiO3M6MTc6ImNzc19tYWluX2JnX2NvbG9yIjtzOjExOiJ0cmFuc3BhcmVudCI7czoyMToiY3NzX21haW5fYm9yZGVyX2NvbG9yIjtzOjExOiJ0cmFuc3BhcmVudCI7czoxMToidGl0bGVfY29sb3IiO3M6MDoiIjtzOjE4OiJtb2R1bGVfaW5zdGFuY2VfaWQiO3M6MToiNSI7czo3OiJwb3N0X2lkIjtzOjM6IjU4NyI7czoxMToiZHNsY19tX3NpemUiO3M6MjoiMTIiO3M6OToibW9kdWxlX2lkIjtzOjEwOiJEU0xDX1Bvc3RzIjtzOjE2OiJkc2xjX21fc2l6ZV9sYXN0IjtzOjM6InllcyI7fQ==[/dslc_module] [/dslc_modules_area] [/dslc_modules_section] ';

	$new_search_listing_post = array(
	  'post_content' => '', // The full text of the post.
	  'post_name'    => 'search-results-listing-template', // The name (slug) for your post
	  'post_title'   => LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, // The title of your post.
	  'post_status'  => 'publishs', // Default 'draft'.
	  'post_type'    => 'lbmn_archive', // Default 'post'.
	);

	$new_search_listing_id = wp_insert_post( $new_search_listing_post);
	add_post_meta($new_search_listing_id, 'dslc_code', $new_search_listing_code);

	if ( $new_search_listing_id ) {
		$current_lc_archive_options['search_results'] = $new_search_listing_id;
	}

	update_option( 'dslc_plugin_options_archives', $current_lc_archive_options );

	update_option( 'lbmn_update_ver_181', 1);
}